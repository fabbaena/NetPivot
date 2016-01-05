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

$sesion = new StartSession();
$usuario = $sesion->get('usuario');
$id = $sesion->get('id');
$user_type = $sesion->get('type'); 

$uuid = htmlspecialchars($_GET['uuid']);
$filename = htmlspecialchars($_GET['filename']);
$c_file = $uuid.'_'.$filename.'.conf';

$error_name = $uuid . '_error.txt';
$stats_name = $uuid . '_stats.txt';
$command = './f5conv -f files/' . $uuid . ' -d 2 -e files/' . $error_name . ' -s -S files/'. $stats_name . ' -o files/' . $c_file;


try {
    $pwd = exec($command, $pwd_out,$pwd_error); //this is the command executed on the host  
    $time = new TimeManager();
    $time->Today_Date();
    $today = $time->full_date;                        
    $model = new Crud();
    $model->insertInto = 'conversions';
    $model->insertColumns = 'users_id,time_conversion,files_uuid,converted_file,error_file,stats_file';
    $model->insertValues = "'$id','$today','$uuid','$c_file','$error_name','$stats_name'";
    $model->Create();
    $msg = $model->mensaje;
    if ($msg == true) {
        $sesion->set('uuid', $uuid);
        header ('location:stats.php');
    }
    else {
        header ('location:command.php?error');
    }
} catch (Exception $ex) {
    header ('location:command.php?fatal');
}

