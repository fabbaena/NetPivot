<?php
require_once '../model/StartSession.php';
require_once '../model/UserList.php';
require_once '../engine/Config.php';
require_once '../model/F5Features.php';

$session = new StartSession();
$user = $session->get('user');
$uuid = $session->get('uuid');
if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: /'); 
    exit();
}

if(!isset ($_GET['module']) ) {
    json_encode("No data");
    exit();
}

$c = new Config($uuid);

$npmodules2 = $session->get('npmodules2');
$uuid = $session->get('uuid');
$module = $_GET['module'];
$object_group = $_GET['object_group'];

if(!isset($npmodules2[$module]["object_groups"][$object_group]["objects"]) || $c->ignore_cache()) {
    if($module == 'rule') {
        $data = array('files_uuid' => $uuid, 'name' => $module);
        $m = new F5Module($data);
        $m->load(array_keys($data));
        $m->loadChild(array('name' => $object_group));
        $rule = &$m->_objects[$object_group];

        $handle = file($c->f5_file());
        for($i=$rule->line-1; $i < $rule->lineend; $i++) {
            $out[$i + 1]["source"] = $handle[$i];
            $out[$i + 1]["converted"] = -1;
        }
        echo json_encode($out);
        exit();
    }
    $data = array('files_uuid' => $uuid, 'name' => $module);
    $f = new F5Feature($data);
    if(!$f->load(array_keys($data))) {
        echo json_encode("No Data");
        exit();
    }
    $f->loadChild(array('name' => $object_group));
    $m = &$f->_modules[$object_group];
    $m->loadChildren();

    $npm = &$npmodules2[$module]["object_groups"][$object_group]["objects"];

    foreach($m->_objects as &$o) {

        $p_converted = ($o->total_attr && $o->total_attr > 0) ?
                        round($o->conv_attr / $o->total_attr * 100) : 0;

        $npm[$o->name]["id"]                  = $o->id;
        $npm[$o->name]["name"]                = $o->name;
        $npm[$o->name]["attribute_count"]     = $o->total_attr;
        $npm[$o->name]["attribute_converted"] = $o->conv_attr;
        $npm[$o->name]["attribute_omitted"]   = 0;
        $npm[$o->name]["orphan"]              = $o->orphan;
        $npm[$o->name]["line"]                = $o->line;
        $npm[$o->name]["lineend"]             = $o->lineend;
        $npm[$o->name]["p_converted"]         = $p_converted;
    }

    $session->set('npmodules2', $npmodules2);

} else {
    $npm = &$npmodules2[$module]["object_groups"][$object_group]["objects"];
}


echo json_encode($npm);
?>