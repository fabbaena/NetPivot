<?php
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/Conversions.php';

$session = new StartSession();
$user = $session->get('user');

$namemap = array( 
	"ltm" => "LOADBALANCING",
	"apm" => "AAA",
	"gtm" => "GSLB",
	"asm" => "APPFIREWALL"
	);

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: /'); 
    exit();
}
$uuid = $session->get('uuid');
$npmodules2 = $session->get('npmodules2');

if(!isset($npmodules2) || !$npmodules2 || true) {
	$npmodules2 = [];
	$c = new Conversion(array('files_uuid' => $uuid));
	$c->load('files_uuid');
	if(!$c->loadChildren()) {
		error_log("could not load features");
	}
	$i = 0;
	foreach($c->_features as &$f) {
		$npmodules2[$f->name] = [];
		$npm = &$npmodules2[$f->name];

		$p_converted = ($f->attributes && $f->attributes > 0) ?
						round($f->converted / $f->attributes * 100) : 0;

		$npm["id"]                  = $f->id;
		$npm["friendly_name"]       = $f->name;
	    $npm["objgrp_count"]        = $f->modules;
	    $npm["object_count"]        = $f->objects;
	    $npm["attribute_count"]     = $f->attributes;
	    $npm["attribute_converted"] = $f->converted;
	    $npm["attribute_omitted"]   = 0;
	    $npm["p_converted"]         = $p_converted;
	    $npm["ns_name"]             = isset($namemap[$f->name]) ? $namemap[$f->name] : "";

		if($f->name == 'ltm') {
			$f->loadChild(array('name' => 'rule'));
			if(isset($f->_modules['rule'])) {
				$rule = &$f->_modules['rule'];
				$npmodules2['rule'] = [];
				$npm2 = &$npmodules2['rule'];

				$p_converted = ($rule->attributes && $rule->attributes > 0) ?
					round($rule->converted / $rule->attributes * 100) : 0;

		        $npm2["id"]                  = $rule->id;
		        $npm2["friendly_name"]       = "iRULE";
		        $npm2["object_count"]        = $rule->objects;
		        $npm2["attribute_count"]     = $rule->attributes;
		        $npm2["attribute_converted"] = $rule->converted;
		        $npm2["attribute_omitted"]   = 0;
		        $npm2["ns_name"]             = "APPEXPERT";
			    $npm2["p_converted"]         = $p_converted;

		        $npm["attribute_count"]     -= $npm2["attribute_count"];
		        $npm["attribute_converted"] -= $npm2["attribute_converted"];
		        $npm["attribute_omitted"]   -= 0;
				$i++;
			} 
		} 

	    $i++;
	}
	//$npmodules2["_data"]["module_count"] = $i;
	$npmodules2["_data"]["attribute_count"] = $c->attribute_count;
	$npmodules2["_data"]["attribute_converted"] = $c->attribute_converted;
	$npmodules2["_data"]["object_count"] = $c->object_count;
	$npmodules2["_data"]["module_count"] = $c->module_count;
	$npmodules2["_data"]["feature_count"] = $c->feature_count;
	$npmodules2["_data"]["np_version"] = $c->np_version;
	$npmodules2["_data"]["f5_version"] = $c->f5_version;

	$session->set('npmodules2', $npmodules2);
}

echo json_encode($npmodules2)
?>
