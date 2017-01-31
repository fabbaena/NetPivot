<?php

require_once '../model/StartSession.php';
require_once '../model/FileManager.php';
require_once '../model/UserList.php';
require_once '../model/Conversions.php';
require_once '../engine/Config.php';
require_once '../model/Event.php';


$session    = new StartSession();
$user = $session->get('user');

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) { 
    header('location: /'); 
    exit();
}

/* Need to validate uuid is owned by the user before proceding */
$out = array();

try {

	if(!isset($_GET['uuid'])) throw new Exception("Did not provide file to purge.");
	$uuid = htmlspecialchars($_GET['uuid']);


	$c = new Config($uuid);
	$c->convert_orphan(true);

    $file = new FileManager(array('uuid' => $uuid));
    if(!$file->load('uuid')) 
        throw new Exception('Cannot load file data.');

    $file->delete();
    if(!$file->save()) 
        throw new Exception('Cannot save file data.');


    if(file_exists($c->stats_file()))
        unlink($c->stats_file());
    if(file_exists($c->error_file()))
        unlink($c->error_file());
    if(file_exists($c->json_file()))
        unlink($c->json_file());
    if(file_exists($c->ns_file()))
        unlink($c->ns_file());

    $out['result'] = 'ok';
    $out['message'] = 'Data purged from database.';
} catch (Exception $ex) {
	$out['result'] = 'error';
	$out['message'] = $ex->getMessage();
    new Event($user, $ex->getMessage());
    error_log($ex->getMessage());
}
echo json_encode($out);
?>