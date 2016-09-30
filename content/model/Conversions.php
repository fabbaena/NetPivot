<?php 

require_once "NPObject.php";
require_once "F5Features.php";
require_once "F5Objects.php";
require_once "Crud.php";

class Conversion extends NPObject {
	public $id;
	public $users_id;
	public $conversion_time;
	public $files_uuid;
	public $converted_file;
	public $error_file;
	public $stats_file;
	public $json_file;
	public $_features;

	function __construct($record = null) {
		$this->_tablename = "conversions";
		if(!isset($record)) return;
		foreach($this as $key => $value) {
			if(isset($record[$key])) {
				$this->$key = $record[$key];
			}
		}
	}

	function loadJSON($data) {
		foreach($data as $NPModule => $NPMi) {
			if(!is_array($NPMi)) continue;
			foreach($NPMi as $object) {
				if(!is_array($object)) continue;
				$object['files_uuid'] = $this->files_uuid;
				$o = new F5Object(true, $object);
				$feature_name = $o->feature;
				if(!isset($this->_features[$feature_name])) {
					$this->_features[$feature_name] = 
						new F5Feature(array(
							'files_uuid' => $this->files_uuid, 
							'name' =>$feature_name,
							'conversion_id' => $this->id));
				}
				$this->_features[$feature_name]->addObject($o);
			}
		}
	}

	function getFeatureStats() {
		if(!isset($this->_features)) return;
		foreach($this->_features as $feature_name => $f) {
			$f->getStats();
		}
	}

	function getModuleStats($feature) {
		if(!isset($this->_features[$feature])) return;
		$this->_features[$feature]->getModuleStats();
	}

	function printObject($feature, $module, $name) {
		if(!isset($this->_features[$feature])) return;
		$this->_features[$feature]->printObject($module, $name);
	}

	function saveObject($feature, $module, $name) {
		if(!isset($this->_features[$feature])) return;
		$this->_features[$feature]->saveObject($module, $name);
	}

	function saveFeature($feature) {
		if(!isset($this->_features[$feature])) return;
		$this->_features[$feature]->save();
	}

	function saveModule($feature, $module) {
		if(!isset($this->_features[$feature])) return;
		$this->_features[$feature]->saveModule($module);
	}

	function saveData() {
		foreach($this->_features as $f) {
			if(!$f->saveData()) return false;
		}
		return true;
	}

	function loadChildren() {
		$db = new Crud();
		$db->select = "*";
		$db->from = "f5_stats_features";
		$db->condition = array('=' => array('conversion_id' => $this->id));
		$db->Read3();
		if(!isset($db->rows[0])) return false;
		foreach($db->rows as $f) {
			$this->_features[$f['name']] = new F5Feature($f);
		}
		return true;
	}

}
?>