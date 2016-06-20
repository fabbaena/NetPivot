<?php
require '../../model/StartSession.php';
require '../../model/Crud.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
$npmodules2 = $sesion->get('npmodules2');
if($usuario == false || !isset($npmodules2) || !isset ($_GET['module']) || !isset($_GET['object_group'])) { 
    header('location: /'); 
    exit();
}
$module = $_GET['module'];
$object_group = $_GET['object_group'];

if(!isset($npmodules[$module]["object_groups"][$object_group]["objects"]) || true) {
    $npo = new Crud();
    $npo->select='*';
    if($module != 'rule') {
        $npo->from='obj_names_view';
        $npo->condition='obj_grp_id='.$npmodules2[$module]["object_groups"][$object_group]["id"];
    } else {
        $npo->from='obj_names_view';
        $npo->condition='obj_grp_id='.$npmodules2[$module]["id"];
    }
    $npo->Read2();
    $npobjects = $npo->fetchall;
    $out = [];
    foreach($npobjects as $oname => $v) {
        $npobjects[$oname]["attribute_count"] += 0;
        $npobjects[$oname]["attribute_converted"] += 0;
        $npobjects[$oname]["attribute_omitted"] += 0;
        $npobjects[$oname]["line"] += 0;

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