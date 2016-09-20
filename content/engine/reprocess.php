<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../model/StartSession.php';
require '../model/TimeManager.php';
require '../model/Crud.php';
require '../engine/Config.php';

$sesion    = new StartSession();
$usuario   = $sesion->get('usuario');
$id        = $sesion->get('id');
$user_type = $sesion->get('type'); 

if($usuario == false ) { 
    header('location: /'); 
    exit();
}

$uuid  = htmlspecialchars($_GET['file']);


$c = new Config($uuid);
$c->convert_orphan(true);

try {
    $time = new TimeManager();
    $time->Today_Date();
    $today = $time->full_date;

    $file_rec = new Crud();
    $file_rec->select = "*";
    $file_rec->from = "files";
    $file_rec->condition = "uuid='$uuid'";
    $file_rec->Read();
    $file_data = $file_rec->rows[0];

    if(file_exists($c->stats_file()))
        unlink($c->stats_file());
    if(file_exists($c->error_file()))
        unlink($c->error_file());
    if(file_exists($c->json_file()))
        unlink($c->json_file());
    if(file_exists($c->ns_file()))
        unlink($c->ns_file());

    $pwd = exec($c->command(), $pwd_out,$pwd_error); 

    $converted_rec = new Crud();
    $converted_rec->select = "*";
    $converted_rec->from = "conversions";
    $converted_rec->condition = "files_uuid='$uuid'";
    $converted_rec->Read();
    $converted_data = $converted_rec->rows[0];


    $model = new Crud();
    $model->deleteFrom = 'files';
    $model->condition = "uuid='$uuid'";
    $model->Delete();

    $model = new Crud();
    $model->insertInto = "files";
    $model->data = $file_data;
    $model->Create2();

    $model = new Crud();
    $model->insertInto = "conversions";
    $model->data = $converted_data;
    $model->Create2();



    $msg = $model->mensaje;
    if ($msg == true) {
        /*
        $load = new Crud();
        $load->filename = $c->stats_file();
        $load->uuid = $uuid;
        $load->Load();
        */
        $sesion->set('uuid', $uuid);

        $string = file_get_contents($c->json_file());
        $json_a = json_decode($string, true);

        $conn = new Crud();
        $conn->uploadJSON2($uuid, $json_a);
        /*
        foreach($json_a as $objectgroup => $obj) {
            uploadJSON($conn, $uuid, $objectgroup, $obj);

        }
        */

        header ('location:../dashboard/content.php');
    }
    else {
        header ('location:command.php?error');
    }
} catch (Exception $ex) {
    header ('location:command.php?fatal');
}

