<?php
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/F5Objects.php';

$session = new StartSession();
$user = $session->get('user');

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: /'); 
    exit();	
}
$uuid = $session->get('uuid');
$npmodules2 = $session->get('npmodules2');

try {
	if(!isset($_GET['objid'])) throw new Exception("No data as input", 1);
	$objid = $_GET['objid'];
	$o = new F5Object(false, array('files_uuid' => $uuid, 'id' => $objid));
	if(!$o->load('id')) throw new Exception("No data found", 1);
	
} catch (Exception $e) {
	echo json_encode($e->getMessage());
	exit();
}
echo $o->attributes;
?>