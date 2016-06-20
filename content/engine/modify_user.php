<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/StartSession.php';
require '../model/Crud.php';
require '../model/CheckUser.php';
require '../model/UserList.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario');
$id= $sesion->get('id'); 
$user_type = $sesion->get('type');
$roles = $sesion->get('roles');

$modified = $_GET;

if($usuario == false || !isset($roles[1]) || !isset($modified["id"])) {
    header('location: ../');
    exit();
}

$user = new User();
$user->id = $modified["id"];
$user->load();

$user->modify($modified);
echo json_encode("OK");

?>



