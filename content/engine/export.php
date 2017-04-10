<?php
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/F5Objects.php';
require_once dirname(__FILE__) .'/Config.php';

$session = new StartSession();
$user = $session->get('user');

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: /'); 
    exit();
}

$uuid = $session->get('uuid');
$objid = $_GET["objid"];

$c = new Config($uuid);

$obj = new F5Object(false, array('files_uuid' => $uuid, 'id' => $objid));
$obj->load('id');

$linestart = $obj->line;
$lineend = $obj->lineend;

$handle = file($c->f5_file());

header('Content-type: application/octet-stream');
header('Content-Disposition: attachment;filename="'. $obj->name. '.txt"');

for($i=$linestart-1; $i < $lineend; $i++) {
	echo $handle[$i];
}
?>