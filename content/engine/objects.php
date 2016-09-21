<?php
require '../model/StartSession.php';
require '../model/Crud.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
$npmodules2 = $sesion->get('npmodules2');
if($usuario == false || !isset($npmodules2) || !isset ($_GET['module']) || !isset($_GET['object_group'])) { 
    header('location: /'); 
    exit();
}
$uuid = $sesion->get('uuid');
$module = $_GET['module'];
$object_group = $_GET['object_group'];

if(!isset($npmodules2[$module]["object_groups"][$object_group]["objects"]) || true) {
    $npo = new Crud();
    /*
    $npo->select='*';
    */
    $npo->select='id, name, total_attr as attribute_count, conv_attr as attribute_converted, 0 as attribute_omitted, line, lineend';
    $npo->from='f5_attributes_json';
    if($module != 'rule') {
        $npo->condition="feature='$module' and module='$object_group' and files_uuid='$uuid'";
        /*
        $npo->from='obj_names_view';
        $npo->condition='obj_grp_id='.$npmodules2[$module]["object_groups"][$object_group]["id"];
        */
    } else {
        $npo->condition="feature='ltm' and module='rule' and files_uuid='$uuid'";
        /*
        $npo->from='obj_names_view';
        $npo->condition='obj_grp_id='.$npmodules2[$module]["id"];
        */
    }
    $npo->Read2();
    $npobjects = $npo->fetchall;
    $out = [];
    foreach($npobjects as $oname => $v) {
        $npobjects[$oname]["attribute_count"] += 0;
        $npobjects[$oname]["attribute_converted"] += 0;
        $npobjects[$oname]["attribute_omitted"] += 0;
        $npobjects[$oname]["line"] += 0;
        $npobjects[$oname]["lineend"] += 0;

        if(isset($npobjects[$oname]["attribute_count"]) && $npobjects[$oname]["attribute_count"] > 0) {
            $npobjects[$oname]["p_converted"] = round($npobjects[$oname]["attribute_converted"] / 
                $npobjects[$oname]["attribute_count"] * 100);
        } else {
            $npobjects[$oname]["p_converted"] = 0;
        }
        $out[$npobjects[$oname]["name"]] = $npobjects[$oname];

    }
    $npmodules2[$module]["object_groups"][$object_group]["objects"] = $out;
    $sesion->set('npmodules2', $npmodules2);

} else {
    $out = $npmodules2[$module]["object_groups"][$object_group]["objects"];
}


echo json_encode($out);
?>