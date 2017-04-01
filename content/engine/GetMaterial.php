<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) . '/../model/StartSession.php';
require_once dirname(__FILE__) . '/../model/UserList.php';
require_once dirname(__FILE__) . '/../model/Material.php';


$session = new StartSession();
$user = $session->get('user');

if (!($user && $user->has_role("Engineer"))) {
	echo json_encode(array("message" => "Access Denied", "status" => "error"));
    exit();
}

if(isset($_GET['projectid'])) {
	$project_id=$_GET['projectid'];
} else if(isset($_POST['projectid'])) {
	$project_id = $_POST['projectid'];
}

$materiallist = new MaterialList($project_id);
$materiallist->message = "Line list attached";
$materiallist->status = "ok";


echo json_encode($materiallist);

