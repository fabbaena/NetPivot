<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../model/StartSession.php';
require '../model/TimeManager.php';
require '../model/Crud.php';
require '../model/ConnectionBD.php';
require '../model/Netpivot.php';

$sesion    = new StartSession();
$usuario   = $sesion->get('usuario');
$id        = $sesion->get('id');
$user_type = $sesion->get('type'); 

if($usuario == false ) { 
    header('location: /'); 
    exit();
}

$uuid  = htmlspecialchars($_GET['file']);

include '../engine/Config.php';


try {
    $pwd = exec($command, $pwd_out,$pwd_error); //this is the command executed on the host  
    $time = new TimeManager();
    $time->Today_Date();
    $today = $time->full_date;

    $model = new Crud();
    $model->deleteFrom = 'details';
    $model->condition = "files_uuid='$uuid'";
    $model->Delete();

    $model->deleteFrom = 'modules';
    $model->Delete();

    $msg = $model->mensaje;
    if ($msg == true) {
        $load = new Crud();
        $load->filename = $p_csv_name;
        $load->uuid = $uuid;
        $load->Load();
        $sesion->set('uuid', $uuid);
        header ('location:brief.php');
    }
    else {
        header ('location:command.php?error');
    }
} catch (Exception $ex) {
    header ('location:command.php?fatal');
}

