<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/ConnectionBD.php';
require '../model/Crud.php';

$timezone = htmlspecialchars($_POST['Time_Zone']);


$model = new Crud();
$model->update = 'settings';
$model->set = "timezone='$timezone'";
$model->condition = 'host_name=0';
$model->Update();
$so = $model->mensaje;
if ($so == true) {
    header('location:../dashboard/settings.php?done');
} else {
    header('location:../dashboard/settings.php?error');
}