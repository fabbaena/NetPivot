<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/functions.php';

$session   = new StartSession();
$user   = $session->get('user');

if(!($user && $user->has_role("System Admin")) || !isset($_GET['id'])) {
    header('location: ../');
    exit();
}

$modified = $_GET;

$user = new User(array('id' => $modified["id"]));
$user->load();

$user->modify($modified);
echo json_encode("OK");

?>



