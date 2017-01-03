<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
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
    if(!isset($_GET['uuid'])) throw new Exception("Did not receive any process to monitor.");
    $uuid = $_GET['uuid'];

    $c = new Config($uuid);

    $process_file = $c->json_file(). ".process";

} catch (Exception $ex) {
    error_log($ex->getMessage());
    $process["result"] = "error";
    $process["message"] = $ex->getMessage();
}
$session->set('uuid', $uuid);
echo json_encode($process);