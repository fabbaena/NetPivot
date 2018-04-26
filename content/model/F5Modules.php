<?php

require_once 'NPObject.php';
require_once 'F5Objects.php';

class F5Module extends NPObject {
	public $id;
	public $files_uuid;
	public $feature_id;
	public $name;
	public $objects;
	public $attributes;
	public $converted;
	public $orphan;

	public $_objects;

	function __construct($record = null) {
		$this->_tablename = "f5_stats_modules";
		$this->objects = 0;
		$this->attributes = 0;
		$this->converted = 0;
		$this->orphan = 0;
		if(!isset($record)) return;

		foreach($this as $key => $value) {
			if(isset($record[$key])) $this->$key = $record[$key];
		}
	}

	function addObject(&$o) {
		$this->_objects[$o->name] = $o;
		$this->objects++;
		if($o->isOrphan() != 1) {
			$this->attributes += $o->countAttributes();
			$this->converted += $o->countConverted();
		} else {
			$this->orphan++;
		}
	}

	function countAttributes() {
		return $this->attributes;
	}

	function countConverted() {
		return $this->converted;
	}

	function getStats() {
		echo "module=". $this->name. ", objects=". $this->objects. 
			", attributes=". $this->attributes. ", converted=". $this->converted. "\n";
	}

	function printObject($name) {
		if(!isset($this->_objects[$name])) return;
		$this->_objects[$name]->printObject();
	}

	function saveObject($name) {
		if(!isset($this->_objects[$name])) return;
		$this->_objects[$name]->save();
	}

	function saveData() {
		if(!$this->save()) return false;
		if(!isset($this->_objects)) return false;
		foreach ($this->_objects as &$o) {
			$o->module_id = $this->id;
			if(!$o->save()) return false;
		}
		return true;
	}

	function loadChildren() {
		$db = new Crud();
		$db->select = "*";
		$db->from = "f5_attributes_json";
		$db->condition = array('=' => array('module_id' => $this->id));
		$db->Read3();
		if(!isset($db->rows[0])) return false;
		foreach($db->rows as $f) {
			$this->_objects[$f['name']] = new F5Object(false, $f);
		}
		return true;
	}


	function loadChild($data) {
		$newdata = array_merge(array('module_id' => $this->id), $data);
		$m = new F5Object(false, $newdata);

		if(!$m->load(array_keys($newdata))) {
			return false;
		}
		$this->_objects[$m->name] = &$m;
		return true;
	}

}

?>