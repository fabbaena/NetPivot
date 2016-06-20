<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/Crud.php';

$value = htmlspecialchars($_POST['uuid']);


$model = new Crud();
$model->select ='error_file';
$model->from = 'conversions';
$model->condition = 'files_uuid="'.$value.'"' ;
$model->Read();
$rows = $model->rows;
$mensaje = $model->mensaje;


foreach ($rows as $info){
    $file = $info['error_file'];
}



$file = '../dashboard/files/'. $file;



$basefichero = basename($file);

header( "Content-Type: application/octet-stream");
header( "Content-Length: ".filesize($file));
header( "Content-Disposition: attachment; filename=".$basefichero."");
readfile($file);