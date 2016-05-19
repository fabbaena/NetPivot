<?php
require '../../model/StartSession.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
if($usuario == false || !isset($_GET["s"]) || !isset($_GET["e"])) { 
    header('location: /'); 
    exit();
}
$uuid = $sesion->get('uuid');
$s = $_GET["s"];
$e = $_GET["e"];

include '../../engine/Config.php';


$handle = file($p_f5_file);

for($i=$s,$j=0; $i <= $e; $i++,$j++) {
	$out[$j] = $handle[$i];
}

echo json_encode($out);

?>