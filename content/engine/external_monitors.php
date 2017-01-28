<?php
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/Conversions.php';

$session = new StartSession();
$user = $session->get('user');

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: /'); 
    exit();
}
$uuid = $session->get('uuid');

$ext_monitor = new F5ObjectList(array(
	'files_uuid' => $uuid,
	'feature' => 'ltm',
	'module' => 'monitor',
	'type' => 'external'));

$em = array();
if($ext_monitor->count > 0) {
	foreach($ext_monitor->objects as $key => $o) {
		$c = array();
		foreach($o->attributes as $a => $v) {
			if(is_object($v)) {
				if($v->name == 'run') {
					array_push($em, $v->value);
					break;
				}
			} 
		}
	}

}
echo json_encode($em)
?>