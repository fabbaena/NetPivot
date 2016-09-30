<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/FileManager.php';

$session = new StartSession();
$user = $session->get('user');
if(!$user) {
    header('location: /'); 
    exit();
}

if(!isset($_GET['uuid'])) {
    header ('location: ../dashboard/index.php?deleted_file=false');
    exit();
}

$uuid = htmlspecialchars($_GET['uuid']);
$file = new FileManager(array('uuid' => $uuid));
$file->load('uuid');

if($user->has_role("System Admin") || $file->users_id == $user->id) {
    $file->delete();
} else {
    header ('location: ../dashboard/index.php?permission_denied');
    exit();
}

header ('location: ../dashboard/index.php?deleted_file=true');

?>