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
$npmodules2 = $session->get('npmodules2');
if($npmodules2['_data']['f5_version'] == 'TMSH') {
	$profile_type = 'client-ssl';
} else {
	$profile_type = 'clientssl';
}


$clientssl = new F5ObjectList(array(
	'files_uuid' => $uuid,
	'feature' => 'ltm',
	'module' => 'profile',
	'type' => $profile_type));

$certs = array();
if($clientssl->count > 0) {
	foreach($clientssl->objects as $key => $o) {
		$c = array();
		foreach($o->attributes as $a => $v) {
			if(is_object($v)) {
				if($v->name == 'cert') $c['cert'] = $v->value;
				if($v->name == 'key') $c['key'] = $v->value;
			} 
		}
		if(isset($c['cert']) && isset($c['key']) && $c['cert'] == 'default.crt' && $c['key'] == 'default.key') continue;
		array_push($certs, $c);
	}

}
echo json_encode($certs)
?>