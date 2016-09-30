<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../model/StartSession.php';
require_once '../model/UserList.php';
require_once '../model/FileManager.php';

$session = new StartSession();
$user = $session->get('user');
if(!$user) {
    header('location: /'); 
    exit();
}

if(!isset($_GET['uuid']) || !isset($_GET['newname'])) {
    header ('location: ../dashboard/index.php?renamed_file=false');
    exit();
}

$newname = htmlspecialchars($_GET['newname']);
$uuid = htmlspecialchars($_GET['uuid']);

$file = new FileManager(array('uuid' => $uuid));
$file->load('uuid');

if(!($user && $user->has_role("System Admin")) || $file->users_id != $user->id) {
    header ('location: ../dashboard/index.php?renamed_file=access_denied');
    exit();
}

$file->filename = $newname;
if($file->update()) {
    header('location:../dashboard/index.php?renamed_file=true');
} else {
    header ('location: ../dashboard/index.php?renamed_file=false');	
}


?>
            

