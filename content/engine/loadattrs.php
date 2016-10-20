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
$attrs = json_decode($obj->attributes);

$handle = file($c->f5_file());

for($i=$linestart-1; $i < $lineend; $i++) {
	$out[$i + 1]["source"] = $handle[$i];
	$out[$i + 1]["converted"] = -1;
}

if(count($attrs) > 0) {
	foreach ($attrs as &$a) {
		if(is_string($a)) continue;
		$l = $a->line;
		$out[$l]["name"] = $a->name;
		$out[$l]["converted"] = $a->converted;
	}
}

echo json_encode($out);
?>