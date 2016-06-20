<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../model/Crud.php';

$user_id = htmlspecialchars($_GET['id']);
$password = htmlspecialchars($_POST['password']);
$max_files = htmlspecialchars($_POST['max_files']);
$user_type = htmlspecialchars($_POST['usertype']);
$max_conversions = htmlspecialchars($_POST['max_conversions']);

$number = 100;
    $modelo = new Crud();
    $modelo->update = 'users';
    if (empty($password)== true) {
        $modelo->set = "type='$user_type',max_files='$number',max_conversions='$number'";        
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $modelo->set = "password='$hash',type='$user_type',max_files='$number',max_conversions='$number'";
    }    
    $modelo->condition = 'id='. $user_id;
    $modelo->Update();
    $mensaje = $modelo->mensaje;
    if ($mensaje == true) {
         header ('location:../dashboard/admin_users.php?mod_ok');
    } else {
         header ('location:../dashboard/admin_users.php?mod_error');
    }

