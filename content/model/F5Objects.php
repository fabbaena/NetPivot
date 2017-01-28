<?php

require_once 'NPObject.php';

class F5ObjectList {
	public $files_uuid;
	public $feature;
	public $module;
	public $type;
	public $name;
	public $adminpart;
	public $objects;
	public $count;

	function __construct($record = null) {
		$this->objects = array();
		if(!isset($record)) return;
		foreach($this as $key => $value) {
			if(isset($record[$key])) $this->$key = $record[$key];
		}
		$db = new Crud();
		$db->select = '*';
		$db->from = "f5_attributes_json";
		$db->condition = new Condition('files_uuid', '=', $this->files_uuid);
		if(isset($this->feature))
			$db->condition->add('and', new Condition('feature', '=', $this->feature));
		if(isset($this->module)) 
			$db->condition->add('and', new Condition('module', '=', $this->module));
		if(isset($this->type)) 
			$db->condition->add('and', new Condition('type', '=', $this->type));
		if(isset($this->name)) 
			$db->condition->add('and', new Condition('name', '=', $this->name));
		if(isset($this->adminpart)) 
			$db->condition->add('and', new Condition('adminpart', '=', $this->adminpart));
		$this->count = $db->Read5();

		foreach($db->rows as $f) {
			$o = new F5Object(false, $f);
			$o->attributes = json_decode($f["attributes"]);
			array_push($this->objects, $o);
		}

	}

}


class F5Object extends NPObject {
	public $id;
	public $files_uuid;
	public $ObjectType;
	public $feature;
	public $module;
	public $type;
	public $name;
	public $adminpart;
	public $line;
	public $attributes;
	public $conv_attr;
	public $total_attr;
	public $lineend;
	public $module_id;
	public $_attributes;

	function __construct($is_JSON, $record = null) {
		$this->_tablename = "f5_attributes_json";

		if(!isset($record)) return;
		if(!$is_JSON) {
			foreach($this as $key => $value) {
				if(isset($record[$key])) $this->$key = $record[$key];
			}
			return;
		}

		foreach($this as $key => $value) {
			if(isset($record[$key])) {
				if($key == "attributes") {
					$this->attributes = json_encode($record[$key]);
					$this->_attributes = $record[$key];
					$this->total_attr = 0;
					$this->conv_attr = 0;
					foreach($this->_attributes as $a) {
						if(!is_array($a)) continue;
						$this->total_attr++;
						if($a['converted']) $this->conv_attr++;
					}
				} else {
					$this->$key = $record[$key];
				}
			}
		}		
	}

	function countAttributes() {
		return $this->total_attr;
	}
	function countConverted() {
		return $this->conv_attr;
	}

	function printObject() {
		print_r($this);
	}
}
?>