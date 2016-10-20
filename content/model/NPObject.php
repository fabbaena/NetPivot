<?php

require_once 'Crud.php';

class NPObject {
	public $id;
	protected $_tablename;

	function load($key) {
		if(is_array($key)) return $this->loadMulti($key);
		if(!isset($this->$key)) return false;

		$db = new Crud();
		$db->select = '*';
		$db->from = $this->_tablename;
		$db->condition = array( '=' => array( $key => $this->$key));
		$db->Read3();

		if(!isset($db->rows[0])) {
			//error_log($this->_tablename. " $key ". $this->$key. " not found");
			return false;
		}

		foreach($this as $key => $value) {
			if(isset($db->rows[0][$key])) $this->$key = $db->rows[0][$key];
		}
		return true;
	}

	function loadMulti($m) {
		$db = new Crud();
		$db->select = '*';
		$db->from = $this->_tablename;
		$db->condition["and"] = [];
		foreach($m as $i) {
			array_push($db->condition["and"], array('=' => array( $i => $this->$i)));
		}
		$db->Read4();
		if(!isset($db->rows[0])) {
			return false;
		}

		foreach($this as $key => $value) {
			if(isset($db->rows[0][$key])) $this->$key = $db->rows[0][$key];
		}
		return true;		
	}

	function save() {
		if(isset($this->id)) {
			return $this->update();
		}

		$db = new Crud();
		$db->insertInto = $this->_tablename;
		foreach($this as $key => $value) {
			if($key[0] != '_' && isset($this->$key)) $db->data[$key] = $value;
		}
		$db->Create2('id');
		$this->id = $db->id;
		if(!isset($this->id)) throw new Exception("Unable to save $key into ".$this->_tablename.".");
		return true;
	}

	function update() {
		if(!isset($this->id)) return false;

		$db = new Crud();
		$db->update = $this->_tablename;
		foreach($this as $key => $value) {
			if($key[0] != '_' && $key != 'id' && isset($this->$key)) 
				$db->data[$key] = $value;
		}
		$db->condition = "id=". $this->id;
		$db->Update2();
		return true;
	}

	function delete() {
		if(!isset($this->id)) return false;

		$db = new Crud();
		$db->deleteFrom = $this->_tablename;
		$db->condition = "id=". $this->id;
		$db->Delete();
		$this->id = null;
	}


	function toString() {
		foreach($this as $key => $value) {
			if($key[0] != '_' && isset($this->$key))
				echo "$key=". $this->$key. "\n";
		}
	}
}

?>