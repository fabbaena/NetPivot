<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/StartSession.php';


$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
$uuid = $sesion->get('uuid');
$filename  = $sesion->get('filename');
if($usuario == false || !isset($uuid)) { 
    header('location: /'); 
    exit();
}
require 'Config.php';

$file = "$path_files${uuid}_.conf";


header( "Content-Type: application/octet-stream");
header( "Content-Length: ".filesize($file));
header( "Content-Disposition: attachment; filename=${filename}_ns.conf");
readfile($file);
        