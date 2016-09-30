<?php
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/FileManager.php';

$session = new StartSession();
$user = $session->get('user');
$uuid = $session->get('uuid');
if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: /'); 
    exit();
}

$file = new FileManager(array('uuid' => $uuid));
$file->load('uuid');

$session->set('filename', $file->filename);

echo json_encode($file->filename);
?>