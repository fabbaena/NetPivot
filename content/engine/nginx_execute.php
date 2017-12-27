<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
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
    $projectid = get_int($_GET, 'projectid');

    $command = '/usr/bin/python3 /opt/netpivot/usr/share/nginx/nsconverter.py';
    $args = " --nsout --nsoutfile /var/www/nginx_files/{$uuid}_ns.conf --errorout /var/www/nginx_files/{$uuid}_error.txt /var/www/nginx_files/{$uuid}";
    $pwd = exec($command. $args, $pwd_out,$pwd_error); //this is the command executed on the host  
    if($pwd_error) throw new Exception(
        "There was an error with the conversion process of \"$file_name\". ".
        "Please contact the administrator with the following information ". $uuid);

    $process["result"] = "ok";
    $process["message"] = "Conversion finished.";
    $process["uuid"] = $uuid;
    new Event($user, "Conversion of NginX \"$file_name\" Finished.", 7);
} catch (Exception $ex) {
    new Event($user, $ex->getMessage());
    $process["result"] = "error";
    $process["message"] = $ex->getMessage();
    $session->set('upload_file_name', null);
}

echo json_encode($process);
?>