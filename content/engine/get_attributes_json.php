<?php
require '../model/StartSession.php';
require '../model/Crud.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
$npmodules2 = $sesion->get('npmodules2');
if($usuario == false || !isset($npmodules2) || 
    !isset ($_GET['object_name']) || !isset($_GET['object_group'])) { 
    header('location: /'); 
    exit();
}
$object_name = $_GET['object_name'];
$object_group = str_replace("-", "_", $_GET['object_group']);

$attributes = new Crud();
$attributes->select='attributes';
$attributes->from="f5_${object_group}_json" ;
$attributes->condition="name='$object_name'";

$attributes->Read2();
$a = $attributes->fetchall;

if(isset($a[0])) {
    echo $a[0]["attributes"];
}
?>