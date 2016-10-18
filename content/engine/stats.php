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

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: ../');
    exit();
}


$process = array();
try {
    if(!isset($_GET['uuid'])) throw new Exception("Did not receive any file to convert.");
    $uuid = $_GET['uuid'];

    $conversion = new Conversion(array("files_uuid" => $uuid));
    $conversion->load('files_uuid');

    $c = new Config($uuid);
    $string = file_get_contents($c->json_file());
    $json_a = json_decode($string, true);
    $conversion->loadJSON($json_a);
    $conversion->saveData();

    $process["result"] = "ok";
    $process["message"] = "Statistics Generated.";
    $process["uuid"] = $uuid;
} catch (Exception $ex) {
    error_log($ex->getMessage());
    $process["result"] = "error";
    $process["message"] = $ex->getMessage();
}
$session->set('uuid', $uuid);
echo json_encode($process);