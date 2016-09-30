<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/DomainList.php';

$session = new StartSession();
$user = $session->get('user');

$modified = $_POST;

if(!($user && $user->has_role("System Admin")) || !isset($modified["id"])) {
    header('location: ../');
    exit();
}

$domain = new Domain($modified);
$domain->modify();

header('location: ../admin/admin_domains.php');
?>



