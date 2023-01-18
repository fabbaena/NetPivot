<?php
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/F5Features.php';
require_once dirname(__FILE__) .'/../model/F5Modules.php';
require_once dirname(__FILE__) .'/../model/F5Objects.php';
require_once dirname(__FILE__) .'/../engine/Config.php';

$session = new StartSession();
$user = $session->get('user');

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: /'); 
    exit();    
}

$c = new Config();

$npmodules2 = $session->get('npmodules2');
$module = $_GET['module'];
$uuid = $session->get('uuid');

$name_map = array (
    "module" => array (
        "items"               => "_objects",
        "attribute_count"     => "total_attr",
        "attribute_converted" => "conv_attr",
        "load"                => "loadObjects"),
    "feature" => array(
        "items"               => "_modules",
        "attribute_count"     => "attributes",
        "attribute_converted" => "converted",
        "load"                => "loadModules"),
    );

if(!isset($npmodules2[$module]["object_groups"]) || $c->ignore_cache()) {
    
    $npmodules2[$module]["object_groups"] = [];
    $og = &$npmodules2[$module]["object_groups"];

    if($module == 'rule') {
        $m = new F5Module(array('id' => $npmodules2[$module]['id']));
        $t = &$name_map["module"];
        $feature = false;
    } else {
        $m = new F5Feature(array('id' => $npmodules2[$module]['id']));
        $t = &$name_map["feature"];
        $feature = true;
    }

    $m->load('id');
    $m->loadChildren();

    foreach($m->{$t["items"]} as &$o) {
        $p_converted = ($o->{$t["attribute_count"]} && $o->{$t["attribute_count"]} > 0) ?
                        round($o->{$t["attribute_converted"]} / $o->{$t["attribute_count"]} * 100) : 0;
        if($module == 'rule') {
            $og[$o->name]["id"]              = $o->id;
        }

        $og[$o->name]["name"]                = $o->name;
        $og[$o->name]["attribute_count"]     = $o->{$t["attribute_count"]};
        $og[$o->name]["attribute_converted"] = $o->{$t["attribute_converted"]};
        $og[$o->name]["attribute_ommited"]   = 0;
        $og[$o->name]["orphan"]              = $o->orphan;
        $og[$o->name]["object_count"]        = $feature ? $o->objects : 1;
        $og[$o->name]["p_converted"]         = $p_converted;
    }

    $session->set('npmodules2', $npmodules2);

} 
echo json_encode($npmodules2[$module]["object_groups"]);
?>
