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
    if(!isset($_GET['uuid'])) throw new Exception("Did not receive any file to generate stats.");
    $uuid = $_GET['uuid'];

    $conversion = new Conversion(array("files_uuid" => $uuid));
    $conversion->load('files_uuid');

    $c = new Config($uuid);
    $string = file_get_contents($c->json_file());
    if(strlen($string) < 5) throw new Exception("Internal Error. Stats couldn't be generated for \"$file_name\". ($uuid)");
    $json_a = json_decode($string, true);

    if(isset($json_a['file_info'])) {
        $file_info = $json_a['file_info'];
        unset($json_a['file_info']);
        if(isset($file_info['np_version'])) {
            $conversion->np_version = $file_info['np_version'];
        }
        if(isset($file_info['F5_version'])) {
            $conversion->f5_version = $file_info['F5_version'];
        }
        $conversion->saveVersion();
    }

    if(isset($json_a['OTHER'])) {
        unset($json_a['OTHER']);
    }

    if(count($json_a) < 2) throw new Exception("Internal Error. JSON file error for \"$file_name\". ($uuid)");
    
    if(!$conversion->loadJSON($json_a)) 
        throw new Exception("Internal Error. Cannot load data into memory for \"$file_name\". ($uuid)");
    if(!$conversion->saveData()) 
        throw new Exception("Internal Error. Cannot load JSON data into database for \"$file_name\". ($uuid)");

    $process["result"] = "ok";
    $process["message"] = "Statistics Generated.";
    $process["uuid"] = $uuid;
    new Event($user, "Statistics generated for \"$file_name\"", 8);
} catch (Exception $ex) {
    error_log($ex->getMessage());
    $process["result"] = "error";
    $process["message"] = $ex->getMessage();
    new Event($user, $ex->getMessage());
}
$session->set('upload_file_name', null);
$session->set('uuid', $uuid);
echo json_encode($process);

?>
