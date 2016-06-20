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
$npmodules2 = $sesion->get('npmodules2');
if(!isset($npmodules2) || !$npmodules2 || true) {
	$npmodules2 = [];
	$npm = new Crud();
	$npm->select='*';
	$npm->from='modules_view';
	$npm->condition='files_uuid="'.$uuid.'"';
	$npm->Read();

	$npmodules2["_data"]["files_uuid"] = $uuid;
	$i = 0;
	$totalattributes = 0;
	foreach ($npm->rows as $value) {
		$i++;
		if($value["attribute_count"] && $value["attribute_count"] > 0) {
			$p_converted = round($value["attribute_converted"] / $value["attribute_count"] * 100);
		} else {
			$p_converted = 0;
		}
	    $npmodules2[$value["name"]]["id"]                  = $value["id"];
	    $npmodules2[$value["name"]]["friendly_name"]       = $value["name"];
	    $npmodules2[$value["name"]]["objgrp_count"]        = $value["objgrp_count"];
	    $npmodules2[$value["name"]]["object_count"]        = $value["object_count"];
	    $npmodules2[$value["name"]]["attribute_count"]     = $value["attribute_count"];
	    $npmodules2[$value["name"]]["attribute_converted"] = $value["attribute_converted"];
	    $npmodules2[$value["name"]]["attribute_omitted"]   = $value["attribute_omitted"];
	    $npmodules2[$value["name"]]["p_converted"]         = $p_converted;
	    $totalattributes += $value["attribute_count"];
	    switch ($value["name"]) {
	        case 'ltm':
	            $npmodules2[$value["name"]]["ns_name"] = 'LOADBALANCING';
	            break;
	        case 'apm':
	            $npmodules2[$value["name"]]["ns_name"] = 'AAA';
	            break;
	        case 'gtm':
	            $npmodules2[$value["name"]]["ns_name"] = 'GSLB';
	            break;
	        case 'asm':
	            $npmodules2[$value["name"]]["ns_name"] = 'APPFIREWALL';
	            break;
	        default:
	            $npmodules2[$value["name"]]["ns_name"] = '';
	    }
	    if($value["name"] == "ltm") {
	        $ltmid = $value["id"];
	        $npo = new Crud();
	        $npo->select='*';
	        $npo->from='obj_grps_view';
	        $npo->condition='module_id='.$ltmid.' AND name="rule"';
	        $npo->Read();
			if($npo->rows[0]["attribute_count"] && $npo->rows[0]["attribute_count"] > 0) {
				$p_converted = round($npo->rows[0]["attribute_converted"] / $npo->rows[0]["attribute_count"] * 100);
			} else {
				$p_converted = 0;
			}
			$i++;
	        $npmodules2["rule"]["friendly_name"]       = "iRULE";
	        $npmodules2["rule"]["id"]                  = $npo->rows[0]["id"];
	        $npmodules2["rule"]["object_count"]        = $npo->rows[0]["object_count"];
	        $npmodules2["rule"]["attribute_count"]     = $npo->rows[0]["attribute_count"];
	        $npmodules2["rule"]["attribute_converted"] = $npo->rows[0]["attribute_converted"];
	        $npmodules2["rule"]["attribute_omitted"]   = $npo->rows[0]["attribute_omitted"];
	        $npmodules2["rule"]["ns_name"]             = "APPEXPERT";
		    $npmodules2["rule"]["p_converted"]         = $p_converted;

	        $npmodules2["ltm"]["attribute_count"]     -= $npmodules2["rule"]["attribute_count"];
	        $npmodules2["ltm"]["attribute_converted"] -= $npmodules2["rule"]["attribute_converted"];
	        $npmodules2["ltm"]["attribute_omitted"]   -= $npmodules2["rule"]["attribute_omitted"];
	    }
	}
	$npmodules2["_data"]["module_count"] = $i;
	$sesion->set('npmodules2', $npmodules2);
}

echo json_encode($npmodules2)
?>