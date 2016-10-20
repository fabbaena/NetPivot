<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/DomainList.php';
require_once dirname(__FILE__) .'/../model/UserList.php';

$session = new StartSession();
$user = $session->get('user');
if(!($user && $user->has_role("System Admin"))) {
    header('location: /'); 
    exit();
}

$domain_id = htmlspecialchars($_GET['id']);
try {
    $domain = new Domain(array('id' => $domain_id));
    if(!$domain->load()) {
        header ('location:../admin/admin_domains.php?delete_error');
        exit();
    }

    if ($domain->delete() == true) {
         header ('location:../admin/admin_domains.php?delete_ok');
    } else {
         header ('location:../admin/admin_domains.php?delete_error');
    }
} catch (Exception $ex) {
    header ('location:../admin/admin_domains.php?delete_error');
}

