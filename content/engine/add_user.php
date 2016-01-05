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


$check = new CheckUser();
$check->name = $username;
$check->password = $password;
$check->login();
$msg = $check->mensaje;
$number = 100;
if ($msg == false) 
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $model = new Crud();
        $model->insertInto = 'users';
        $model->insertColumns = 'name,password,type,max_files,max_conversions';
        $model->insertValues = "$username,$hash,'Administrator',$number,$number";
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







