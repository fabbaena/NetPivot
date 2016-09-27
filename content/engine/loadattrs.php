<?php
require '../model/StartSession.php';
require '../model/Crud.php';
require 'Config.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
if($usuario == false || !isset($_GET["objid"])) { 
    header('location: /'); 
    exit();
}
$uuid = $sesion->get('uuid');
$objid = $_GET["objid"];

$c = new Config($uuid);

$objname = new Crud();
$objname->select='line, lineend, attributes';
$objname->from='f5_attributes_json';
$objname->condition="id=$objid";
$objname->Read();
$linestart = $objname->rows[0]["line"];
$lineend = $objname->rows[0]["lineend"];
$attrs = json_decode($objname->rows[0]["attributes"]);

$handle = file($c->f5_file());

for($i=$linestart-1; $i < $lineend; $i++) {
	$out[$i + 1]["source"] = $handle[$i];
	$out[$i + 1]["converted"] = -1;
}


foreach ($attrs as $a) {
	$l = $a->line;
	$out[$l]["name"] = $a->name;
	$out[$l]["converted"] = $a->converted;
}



echo json_encode($out);
?>