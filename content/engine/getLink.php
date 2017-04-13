<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) . '/../model/StartSession.php';
require_once dirname(__FILE__) . '/../model/UserList.php';
require_once dirname(__FILE__) . '/../model/Link.php';


$session = new StartSession();
$user = $session->get('user');

if (!($user && $user->has_role("Engineer"))) {
	echo json_encode(array("message" => "Access Denied", "status" => "error"));
    exit();
}


$link = new Link(array(
	'f5' => get_int($_GET, 'f5'),
	'files_uuid' => $session->get('uuid')
	));

$link->load();
$link->message = "link list attached";
$link->status = "ok";


echo json_encode($link);

?>