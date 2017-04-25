<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) . '/StartSession.php';
require_once dirname(__FILE__) . '/UserList.php';
require_once dirname(__FILE__) . '/F5Features.php';
require_once dirname(__FILE__) . '/F5Modules.php';
require_once dirname(__FILE__) . '/F5Objects.php';
require_once dirname(__FILE__) . '/../engine/Config.php';

class ReportModule {

    public $reportid;
    public $filename;
    public $filters;
    public $locationfile;
    public $uuId;
    public $createdate;
    public $usercreate;
    public $ip;

    function __construct($record = NULL) {
        if ($record != NULL) {
            foreach ($this as $key => $value) {
                if ($key != "roles" && isset($record[$key])) {
                    $this->$key = $record[$key];
                }
            }
        }
    }

    function save($ajax = false) {
        $newreport = new Crud();
        $newreport->insertInto = 'reports';
        $newreport->data = array(
            "filename" => $this->filename,
            "filters" => $this->filters,
            "locationfile" => $this->locationfile,
            "uuid" => $this->uuId,
            "usercreate" => $this->usercreate,
            "createdate" => $this->createdate,
            "ip" => $this->ip
        );
        $newreport->Create2();
        return $newreport->id;
    }

    function getModuleDetails($grupmodule, &$npmodules2) {

        if(!isset($npmodules2[$grupmodule])) return null;
        $module = $grupmodule;
        $name_map = array(
            "module" => array(
                "items" => "_objects",
                "attribute_count" => "total_attr",
                "attribute_converted" => "conv_attr",
                "load" => "loadObjects"),
            "feature" => array(
                "items" => "_modules",
                "attribute_count" => "attributes",
                "attribute_converted" => "converted",
                "load" => "loadModules"),
        );

        if (!isset($npmodules2[$module]["object_groups"])) {
            $npmodules2[$module]["object_groups"] = [];
            $og = &$npmodules2[$module]["object_groups"];

            if ($module == 'rule') {
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

            foreach ($m->$t["items"] as &$o) {
                if($m->name == 'ltm' && $o->name == 'rule') continue;
                $p_converted = ($o->$t["attribute_count"] && $o->$t["attribute_count"] > 0) ?
                        round($o->$t["attribute_converted"] / $o->$t["attribute_count"] * 100) : 0;

                $og[$o->name]["name"] = $o->name;
                //$og[$o->name]["attribute_count"] = $o->$t["attribute_count"];
                //$og[$o->name]["attribute_converted"] = $o->$t["attribute_converted"];
                //$og[$o->name]["attribute_ommited"] = 0;
                $og[$o->name]["object_count"] = $feature ? $o->objects : 1;
                $og[$o->name]["p_converted"] = $p_converted;
            }

        }
        return $npmodules2[$module]["object_groups"];
    }

}
