<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require_once dirname(__FILE__) . '/../model/StartSession.php';
require_once dirname(__FILE__) . '/../model/UserList.php';
require_once dirname(__FILE__) . '/../model/Customer.php';

$session = new StartSession();
$user = $session->get('user');

if (!($user && $user->has_role("Engineer"))) {
	echo json_encode(array("message" => "Access Denied", "status" => "error"));
    exit();
}

$customerlist = new CustomerList(array("usercreate" => $user->id));
$customerlist->message = "Customer list attached";
$customerlist->status = "ok";

echo json_encode($customerlist);

?>