<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/StartSession.php';
require '../model/TimeManager.php';
require '../model/Crud.php';

$sesion    = new StartSession();
$usuario   = $sesion->get('usuario');
$id        = $sesion->get('id');
$user_type = $sesion->get('type'); 
$uuid      = $sesion->get('uuid');

if($usuario == false ) { 
    header('location: /'); 
    exit();
}

include 'Config.php';

$c = new Config($uuid);

try {

    $pwd = exec($c->command(), $pwd_out,$pwd_error); //this is the command executed on the host  
    $time = new TimeManager();
    $time->Today_Date();
    $today = $time->full_date;                        
    $model = new Crud();
    $model->insertInto = 'conversions';
    $model->data = array(
        "users_id"        => $id,
        "time_conversion" => $today,
        "files_uuid"      => $uuid,
        "converted_file"  => $c->ns_file(),
        "error_file"      => $c->error_file(),
        "stats_file"      => $c->stats_file()
        );
    $model->Create2();

    $msg = $model->mensaje;
    if ($msg == true) {

        /****** Loads CSV *****/
        $load = new Crud();
        $load->filename = $c->stats_file();
        $load->uuid = $uuid;
        $load->Load();
        $sesion->set('uuid', $uuid);

        /***** Loads JSON *****/
        $string = file_get_contents($c->json_file());
        $json_a = json_decode($string, true);
        $conn = new Crud();
        foreach($json_a as $objectgroup => $obj) {
            $conn->uploadJSON($uuid, $objectgroup, $obj);

        }

        header ('location:../dashboard/content.php');
    } else {
        header ('location:command.php?error');
    }
} catch (Exception $ex) {
    header ('location:command.php?fatal');
}

