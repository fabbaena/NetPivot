<?php
require_once('/var/www/simplesaml/lib/_autoload.php');
require_once dirname(__FILE__) .'/model/StartSession.php';
require_once dirname(__FILE__) .'/model/UserList.php';
require_once dirname(__FILE__) .'/model/Event.php';

$as = new SimpleSAML_Auth_Simple('NetPivot');
$as->requireAuth();

$attributes = $as->getAttributes();
/*
$attributes["NetPivotUID"][0] = "fabian.baena2@citrix.com";
$attributes["givenName"][0] = "Fabian";
$attributes["sn"][0] = "Baena";
*/

$user = new User(array('name' => $attributes["NetPivotUID"][0]));
$user->load();

$NetPivotUID = $attributes["NetPivotUID"][0];
$givenName = $attributes["givenName"][0];
$sn = $attributes["sn"][0];
$domain = explode("@", $NetPivotUID)[1];

if(!isset($user->id)) {
	$dest = "NetPivotUID=". urlencode($NetPivotUID). 
		"&givenName=". urlencode($givenName). 
		"&sn=". urlencode($sn).
		"&company=Citrix".
        "&company_id=2";
	header("location: register.php?$dest");
} else {
    $session = new StartSession();
    $session->set('usuario',$user->name);
    $session->set('loged', true);
    $session->set('id',$user->id);
    $session->set('type', $user->type);
    $session->set('max_files', $user->max_files);
    $session->set('roles', $user->roles);
    $session->set('starturl', $user->roles[0]->starturl);
    $session->set('user', $user);
    $starturl = $user->roles[0]->starturl;
    new Event($user, "Logged in.", 1);
    header("location: ../". $starturl);
}

?>
