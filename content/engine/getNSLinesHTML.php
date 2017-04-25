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
	header("Location: /");
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

foreach ($link->ns as $nsline) {
	echo "($nsline) ". $handle[$nsline-1];
	echo "<br>";
}
?>