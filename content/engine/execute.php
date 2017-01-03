<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/TimeManager.php';
require_once dirname(__FILE__) .'/../model/Conversions.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/Event.php';
require_once dirname(__FILE__) .'/Config.php';

$session = new StartSession();
$user    = $session->get('user');
$file_name = $session->get('upload_file_name');

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: ../');
    exit();
}

$process = array();
try {
    if(!isset($_GET['uuid'])) throw new Exception("Did not receive any file to convert.");
    $uuid = $_GET['uuid'];

    $c = new Config($uuid);
    $c->convert_orphan(true);
    $pwd = exec($c->command(), $pwd_out,$pwd_error); //this is the command executed on the host  
    if($pwd_error) throw new Exception(
        "There was an error with the conversion process of \"$file_name\". ".
        "Please contact the administrator with the following information ". $uuid);

    $time = new TimeManager();
    $time->Today_Date();
    $today = $time->full_date;
    $conversion = new Conversion(array(
        "users_id"        => $user->id,
        "conversion_time" => $today,
        "files_uuid"      => $uuid,
        "converted_file"  => $c->ns_file(),
        "error_file"      => $c->error_file(),
        "stats_file"      => $c->stats_file(),
        "json_file"       => $c->json_file()
        ));
    ;

    if (!$conversion->save()) {
        throw new Exception("Could not save \"$file_name\" conversion to database. ".
            "Please contact the administrator with the following information: ". $uuid);
        /*
        $string = file_get_contents($c->json_file());
        $json_a = json_decode($string, true);
        $conversion->loadJSON($json_a);
        $conversion->saveData();
        */
    }
    $process["result"] = "ok";
    $process["message"] = "Conversion finished.";
    $process["uuid"] = $uuid;
    new Event($user, "Conversion of \"$file_name\" Finished.", 7);
} catch (Exception $ex) {
    new Event($user, $ex->getMessage());
    $process["result"] = "error";
    $process["message"] = $ex->getMessage();
    $sesion->set('upload_file_name', null);
}

echo json_encode($process);