<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) . '/../model/StartSession.php';
require_once dirname(__FILE__) . '/../model/UserList.php';
require_once dirname(__FILE__) . '/../model/Link.php';
require_once dirname(__FILE__) .'/Config.php';

$session = new StartSession();
$user = $session->get('user');
$uuid = $session->get('uuid');

if (!($user && $user->has_role("Engineer"))) {
	echo json_encode(array("message" => "Access Denied", "status" => "error"));
    exit();
}
$c = new Config($uuid);

$link = new Link(array(
	'f5' => get_int($_GET, 'f5'),
	'f5start' => get_int($_GET, 'f5start'),
	'f5end' => get_int($_GET, 'f5end'),
	'files_uuid' => $uuid
	));

$link->load();

$handle = file($c->ns_file());

$out = array();
$out['message'] = "link list attached";
$out['status'] = "ok";
$out['lines'] = array();
foreach ($link->ns as $nsline) {
	array_push($out['lines'], array("line" => $nsline, "text" => $handle[$nsline-1]));
}

echo json_encode($out);
?>