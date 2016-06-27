<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../model/Crud.php';

$user_id = htmlspecialchars($_GET['id']);
try {
    $model = new Crud();
    $model->deleteFrom = 'conversions';
    $model->condition = "user_id=$user_id";
    $model->Delete();
    $mensaje = $model->mensaje;
    
    if ($mensaje == true) {
        $modelo1 = new Crud();
        $modelo1->deleteFrom = 'files';
        $modelo1->condition = "users_id=$user_id";
        $modelo1->Delete();
        $mensaje1 = $modelo1->mensaje;
    } else {
        header ('location:../admin/admin_users.php?delete_error');
    }
    
    if ($mensaje1 == true) {
        $modelo2 = new Crud();
        $modelo2->deleteFrom = 'users';
        $modelo2->condition = "id=$user_id";
        $modelo2->Delete();
        $mensaje3 = $modelo2->mensaje;
    } else {
         header ('location:../admin/admin_users.php?delete_error');
    }
    
    if ($mensaje3 == true) {
         header ('location:../admin/admin_users.php?delete_ok');
    } else {
         header ('location:../admin/admin_users.php?delete_error');
    }
} catch (Exception $ex) {
    header ('location:../admin/admin_users.php?delete_error');
}

