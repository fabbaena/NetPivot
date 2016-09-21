<?php
require '../model/StartSession.php';
require '../model/Crud.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
$uuid = $sesion->get('uuid');
$npmodules2 = $sesion->get('npmodules2');
if($usuario == false || !isset($npmodules2) || 
    !isset ($_GET['object_name']) || !isset($_GET['object_group'])) { 
    header('location: /'); 
    exit();
}
$objid = $_GET['objid'];

$attributes = new Crud();
$attributes->select='attributes';
$attributes->from="f5_attributes_json" ;
$attributes->condition="id=$objid";

$attributes->Read2();
$a = $attributes->fetchall;

if(isset($a[0])) {
    echo $a[0]["attributes"];
}
?>