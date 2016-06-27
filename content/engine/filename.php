<?php
require '../model/StartSession.php';
require '../model/Crud.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario'); 
$uuid = $sesion->get('uuid');
if($usuario == false || !isset($uuid)) { 
    header('location: /'); 
    exit();
}
$filename = $sesion->get('filename');

if(!isset($filename) || $filename == "" ) {
    $info = new Crud();
    $info->select ='filename';
    $info->from='files';
    $info->condition="uuid='$uuid'";
    $info->Read();
    $filename = $info->rows[0]["filename"];
    $sesion->set('filename', $filename);
}

echo json_encode($filename);
?>