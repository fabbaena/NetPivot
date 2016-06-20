<?php
require '../model/StartSession.php';
require '../model/Crud.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
if($usuario == false || !isset($_GET["objid"])) { 
    header('location: /'); 
    exit();
}
$uuid = $sesion->get('uuid');
$objid = $_GET["objid"];

include '../../engine/Config.php';

$objname = new Crud();
$objname->select='line';
$objname->from='obj_names';
$objname->condition="id=$objid";
$objname->Read();
$linestart = $objname->rows[0]["line"] - 1;

$objidnxt = $objid + 1;
$objname = new Crud();
$objname->select='line';
$objname->from='obj_names';
$objname->condition="id=$objidnxt";
$objname->Read();
$lineend = $objname->rows[0]["line"] - 2;

$handle = file($p_f5_file);

for($i=$linestart; $i <= $lineend; $i++) {
	$out[$i + 1]["source"] = $handle[$i];
	$out[$i + 1]["converted"] = -1;
}

$attrs = new Crud();
$attrs->select='name, converted, line';
$attrs->from='attributes';
$attrs->condition="obj_name_id=$objid";
$attrs->Read();
foreach ($attrs->rows as $a) {
	$l = $a["line"];
	$out[$l]["name"] = $a["name"];
	$out[$l]["converted"] = $a["converted"];
}
$out[$linestart + 1]["converted"] = "-2";
$out[$lineend + 1]["converted"] = "-2";



echo json_encode($out);
?>