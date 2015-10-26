<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/Crud.php';
require '../model/ConnectionBD.php';
require '../model/CheckUser.php';


$username = htmlspecialchars($_POST['username']);
$password = htmlspecialchars($_POST['password']);
$type = htmlspecialchars($_POST['usertype']);
$max_files = htmlspecialchars($_POST['max_files']);
$max_conversions = htmlspecialchars($_POST['max_conversions']);

$check = new CheckUser();
$check->name = $username;
$check->password = $password;
$check->login();
$msg = $check->mensaje;

if ($msg == false) 
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $model = new Crud();
        $model->insertInto = 'users';
        $model->insertColumns = 'name,password,type,max_files,max_conversions';
        $model->insertValues = "'$username','$hash','$type','$max_files','$max_conversions'";
        $model->Create();
        $mensaje = $model->mensaje;
        if ($mensaje == true) {
            header ('location:../dashboard/admin_users.php?new_done');
        } else {
            header ('location:../dashboard/admin_users.php?new_error');
        }    
    } elseif ($msg == true) {
        header ('location:../dashboard/admin_users.php?user_exists');
    }   







