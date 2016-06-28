<?php
require '../model/StartSession.php';
require '../model/Crud.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
$npmodules2 = $sesion->get('npmodules2');
if($usuario == false || !isset($npmodules2) || !isset ($_GET['module'])) { 
    header('location: /'); 
    exit();
}
$module = $_GET['module'];

if(!isset($npmodules[$module]["object_groups"]) || true) {
    $npo = new Crud();
    $npo->select='*';
    if($module != 'rule') {
        $npo->from='obj_grps_view';
        $npo->condition='module_id='.$npmodules2[$module]["id"];
    } else {
        $npo->from='obj_names_view';
        $npo->condition='obj_grp_id='.$npmodules2[$module]["id"];
    }
    $npo->Read2();
    $npobjgrp = $npo->fetchall;
    $out = [];
    foreach($npobjgrp as $ogname => $v) {
        $npobjgrp[$ogname]["attribute_count"] += 0;
        $npobjgrp[$ogname]["attribute_converted"] += 0;
        $npobjgrp[$ogname]["attribute_omitted"] += 0;
        if(isset($npobjgrp[$ogname]["object_count"]) ) {
            $npobjgrp[$ogname]["object_count"] += 0;
        }

        if(isset($npobjgrp[$ogname]["attribute_count"]) && $npobjgrp[$ogname]["attribute_count"] > 0) {
            $npobjgrp[$ogname]["p_converted"] = round($npobjgrp[$ogname]["attribute_converted"] / 
                $npobjgrp[$ogname]["attribute_count"] * 100);
        } else {
            $npobjgrp[$ogname]["p_converted"] = 0;
        }
        $out[$npobjgrp[$ogname]["name"]] = $npobjgrp[$ogname];

    }
    $npmodules2[$module]["object_groups"] = $out;
    $sesion->set('npmodules2', $npmodules2);

} else {
    $out = $npmodules2[$module]["object_groups"];
}


echo json_encode($out);
?>