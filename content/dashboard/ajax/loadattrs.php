<?php
require '../../model/StartSession.php';
require '../../model/Crud.php';
require '../../model/ConnectionBD.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
if($usuario == false || !isset($_GET["objid"])) { 
    header('location: /'); 
    exit();
}
$objid = $_GET["objid"];

$attrs = new Crud();
$attrs->select='*';
$attrs->from='attributes';
$attrs->condition="obj_name_id=$objid";
$attrs->Read();
foreach ($attrs->rows as $a) {
    $attributes[$a["name"]] = $a;
}

$objname = new Crud();
$objname->select='*';
$objname->from='obj_names';
$objname->condition="id=$objid";
$objname->Read();
$attributes["_linestart"] = $objname->rows[0]["line"] - 1;

$objid = $objid + 1;
$objname = new Crud();
$objname->select='*';
$objname->from='obj_names';
$objname->condition="id=$objid";
$objname->Read();
$attributes["_lineend"] = $objname->rows[0]["line"] - 2;

echo json_encode($attributes);
?>