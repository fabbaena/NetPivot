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
$roles = $sesion->get('roles');

if($usuario == false || !isset($roles[1]) || !isset($_GET['id'])) {
    header('location: ../');
    exit();
}


$domain_id = htmlspecialchars($_GET['id']);
try {
    $model = new Crud();
    $model->deleteFrom = 'domains';
    $model->condition = "id=$domain_id";
    $model->Delete();
    $mensaje = $model->mensaje;

    if ($mensaje == true) {
         header ('location:../admin/admin_domains.php?delete_ok');
    } else {
         header ('location:../admin/admin_domains.php?delete_error');
    }
} catch (Exception $ex) {
    header ('location:../admin/admin_domains.php?delete_error');
}

