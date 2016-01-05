<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require 'ConnectionBD.php';
require 'CheckUser.php';
require 'StartSession.php';
require 'Crud.php';

$model = new CheckUser();
$model->name = htmlspecialchars($_POST['inputUsername']);
//$model->password = htmlspecialchars(sha1($_POST['inputPassword']));
$model-> password = htmlspecialchars($_POST['inputPassword']);
$model->login();
$mensaje = $model->mensaje;
$id = $model->id;
$type = $model->user_type;
$max_files = $model->max_files;

if ($mensaje == true) { //Establish all the parameters for the session
    $sesion = new StartSession();
    $usuario = htmlspecialchars($_POST['inputUsername']);
    $sesion->set('usuario',$usuario);
    $sesion->set('loged', true);
    $sesion->set('id',$id);
    $sesion->set('type', $type);
    $sesion->set('max_files', $max_files);
     header("location: ../dashboard/index.php");
} else {
    header ('location:../index.php?error=1');
}
        