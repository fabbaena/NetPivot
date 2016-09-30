<?php

require_once 'NPObject.php';
require_once 'F5Modules.php';
require_once 'F5Objects.php';

class F5Feature extends NPObject {
	public $id;
	public $files_uuid;
	public $name;
	public $objects;
	public $attributes;
	public $converted;
	public $modules;
	public $conversion_id;

	public $_modules;

	function __construct($record = null) {
		$this->_tablename = "f5_stats_features";
		$this->objects = 0;
		$this->attributes = 0;
		$this->converted = 0;
		$this->modules = 0;
		if(!$record) return;
		foreach($this as $key => $value) {
			if(isset($record[$key])) 
				$this->$key = $record[$key];
		}
	}

	function addObject(&$o) {
		$module_name = $o->module;
		if(!isset($this->_modules[$module_name])) {
			$this->_modules[$module_name] = new F5Module(array(
				'files_uuid' => $this->files_uuid, 
				'name' => $module_name));
			$this->modules++;
		}
		$this->_modules[$module_name]->addObject($o);
		$this->attributes += $o->countAttributes();
		$this->converted += $o->countConverted();
		$this->objects++;
	}

	function countAttributes() {
		return $this->attributes;
	}

	function countConverted() {
		return $this->converted;
	}

	function getStats() {
		echo "Feature=". $this->name. ", modules=". $this->modules.
			", objects=". $this->objects. ", attributes=". $this->attributes.
			", converted=". $this->converted. "\n";
	}

	function getModuleStats() {
		if(!isset($this->_modules)) return;
		foreach($this->_modules as $module_name => $m) {
			echo "Feature=". $this->name. ", ";
			$m->getStats();
		}
	}

	function printObject($module, $name) {
		if(!isset($this->_modules[$module])) return;
		$this->_modules[$module]->printObject($name);
	}

	function saveModule($module) {
		if(!isset($this->_modules[$module])) return;
		$this->_modules[$module]->feature_id = $this->id;
		$this->_modules[$module]->save();
	}

	function saveObject($module, $name) {
		if(!isset($this->_modules[$module])) return;
		$this->_modules[$module]->saveObject($name);
	}

	function saveData() {
		if(!$this->save()) return false;
		if(!isset($this->_modules)) return false;
		foreach($this->_modules as &$m) {
			$m->feature_id = $this->id;
			if(!$m->saveData()) return false;
		}
		return true;
	}

	function loadChildren() {
		$db = new Crud();
		$db->select = "*";
		$db->from = "f5_stats_modules";
		$db->condition = array('=' => array('feature_id' => $this->id));
		$db->Read3();
		if(!isset($db->rows[0])) return false;
		foreach($db->rows as $f) {
			$this->_modules[$f['name']] = new F5Module($f);
		}
		return true;
	}


	function loadChild($data) {
		$newdata = array_merge(array('feature_id' => $this->id), $data);
		$m = new F5Module($newdata);

		if(!$m->load(array_keys($newdata))) {
			return false;
		}
		$this->_modules[$m->name] = &$m;
		return true;
	}

}

?>