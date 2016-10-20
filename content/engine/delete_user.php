<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/functions.php';


$session = new StartSession();
$user = $session->get('user');

if(!($user && $user->has_role("System Admin"))) {
    header('location: ../');
    exit();
}

$user_id = get_int($_GET, 'id');
try {
    $u = new User(array('id' => $user_id));
    if(!$u->load()) throw new Exception('');
    $u->delete();
} catch (Exception $e) {
    header ('location:../admin/admin_users.php?delete_error');
}
header ('location:../admin/admin_users.php?delete_ok');


