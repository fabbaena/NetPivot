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
require_once dirname(__FILE__) .'/Config.php';

$session = new StartSession();
$user    = $session->get('user');
$uuid    = $session->get('uuid');

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: ../');
    exit();
}

$c = new Config($uuid);
$c->convert_orphan(true);

try {

    $pwd = exec($c->command(), $pwd_out,$pwd_error); //this is the command executed on the host  
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

    if ($conversion->save()) {
        $string = file_get_contents($c->json_file());
        $json_a = json_decode($string, true);
        $conversion->loadJSON($json_a);
        $conversion->saveData();

        header ('location:../dashboard/content.php');
    } else {
        header ('location:command.php?error');
    }
} catch (Exception $ex) {
    header ('location:command.php?fatal');
}

