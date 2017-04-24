<?php 

require_once "NPObject.php";
require_once "F5Features.php";
require_once "F5Objects.php";
require_once "Crud.php";
require_once "Event.php";

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
	public $np_version;
	public $f5_version;
	public $projectid;
	public $attribute_count;
	public $attribute_converted;
	public $object_count;
	public $module_count;
	public $feature_count;

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
		$total_count = 0;
		$cur_count = 0;
		$perc = 0;
		$process_file = $this->json_file. ".process";
		foreach($data as $k1 => $v1) {
			foreach($v1 as $k2 => $v2) {
				$total_count++;
			}
		}
		try {
			file_put_contents( $process_file , "Starting process $total_count objects\n");
			foreach($data as $NPModule => $NPMi) {
				if(!is_array($NPMi)) continue;
				foreach($NPMi as $object) {
					$cur_count++;
					if(intval($cur_count / $total_count * 100) > $perc) {
						$perc = intval($cur_count / $total_count * 100);
						file_put_contents( $process_file , "$perc\n", FILE_APPEND);
					}
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
		} catch(Exception $ex) {
			new Event($user, $ex->getMessage);
			return false;
		}
		return true;
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

	function saveVersion() {
		$db = new Crud();
		$db->update = "conversions";
		$db->data["np_version"] = $this->np_version;
		$db->data["f5_version"] = $this->f5_version;
		$db->condition = new Condition(new Column('id'), '=', new Value($this->id));
		$db->Update3();
	}

	function saveData() {
		$modules = 0;
		$objects = 0;
		$attributes = 0;
		$converted = 0;
		foreach($this->_features as $f) {
			$modules += $f->modules;
			$objects += $f->objects;
			$attributes += $f->attributes;
			$converted += $f->converted;
			if(!$f->saveData()) return false;
		}
		$db = new Crud();
		$db->update = "conversions";
		$db->data["feature_count"] = count($this->_features);
		$db->data["module_count"] = $modules;
		$db->data["object_count"] = $objects;
		$db->data["attribute_count"] = $attributes;
		$db->data["attribute_converted"] = $converted;
		$db->condition = new Condition(new Column('id'), '=', new Value($this->id));
		$db->Update3();
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