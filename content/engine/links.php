<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/Conversions.php';
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
    if(!isset($_GET['uuid'])) throw new Exception("Did not receive any file to generate links.");
    $uuid = $_GET['uuid'];

    $conversion = new Conversion(array("files_uuid" => $uuid));
    $conversion->load('files_uuid');

    $c = new Config($uuid);
    $string = file_get_contents($c->f5nslink_file());
    if(strlen($string) < 5) throw new Exception("Internal Error. Links couldn't be generated for \"$file_name\". ($uuid)");
    $link_array = explode("\n", $string);

    $conn = new Crud();
    $conn->insertInto = "f5nslink";
    $conn->CreateBulk(true, array("f5", "ns", "files_uuid"));

    foreach($link_array as $l) {
        if($l == "") continue;
        $link = explode(",", $l);
        array_push($link, $uuid);

        $conn->data = $link;
        if($conn->CreateBulk() !== true) throw new Exception("DB error loading links. Details: ". $conn->mensaje);
    }

    $process["result"] = "ok";
    $process["message"] = "Links Generated.";
    $process["uuid"] = $uuid;
    new Event($user, "Links generated for \"$file_name\"", 8);
} catch (Exception $ex) {
    error_log($ex->getMessage());
    $process["result"] = "error";
    $process["message"] = $ex->getMessage();
    new Event($user, $ex->getMessage());
}
echo json_encode($process);

?>