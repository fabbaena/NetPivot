<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';

$session = new StartSession();
$user = $session->get('user');
if(!($user && $user->has_role("System Admin"))) {
    header('location: ../'); 
    exit();
}

$rolelist = new RoleList();

echo json_encode($rolelist);
?>