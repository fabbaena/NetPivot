<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) . '/../model/StartSession.php';
require_once dirname(__FILE__) . '/../model/UserList.php';
require_once dirname(__FILE__) . '/../model/Project.php';


$session = new StartSession();
$user = $session->get('user');

if (!($user && $user->has_role("Engineer"))) {
	echo json_encode(array("message" => "Access Denied", "status" => "error"));
    exit();
}

$customerid = get_int($_GET, 'customerid');

$filter = array('usercreate' => $user->id);
if(isset($customerid)) $filter['customerid'] = $customerid;

$projectlist = new Projectlist($filter);
$projectlist->message = "Line list attached";
$projectlist->status = "ok";


echo json_encode($projectlist);

