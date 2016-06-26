<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../model/StartSession.php';
require '../model/Crud.php';
require '../model/UserList.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario');
$id= $sesion->get('id'); 
$user_type = $sesion->get('type');
$roles = $sesion->get('roles');


if($usuario == false || !isset($roles[1]) || !isset($_GET['id'])) {
    header('location: ../');
    exit();
}
$user_id = htmlspecialchars($_GET['id']);

$user = new User();
$user->id = $user_id;
$user->load();

echo json_encode($user);
?>