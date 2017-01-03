<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template filename, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileManager
 *
 * @author gonzalorodriguez
 */

require_once 'NPObject.php';
require_once 'Conversions.php';
require_once 'Crud.php';

class FileManager extends NPObject {
	public $uuid;
	public $filename;
	public $upload_time;
	public $users_id;
	public $_message;
	public $_conversion;
	private $_path_files;
	public $opportunity_id;

	function __construct($record = null) {
		$this->_tablename = "files";
		if(!is_array($record)) return;

		foreach($this as $key => $value) {
			if(isset($record[$key])) $this->$key = $record[$key];
		}

	}

	public function DeleteFile() {
		$delete = $this->filename;
		try {
			unlink($this->_path_files . $delete);
			$this->_message = 'true';
		} catch (Exception $ex) {
			$error = $e->getMessage();
			$this->_message = $error;
		}

	}

	function save() {

		$db = new Crud();
		$db->insertInto = $this->_tablename;
		foreach($this as $key => $value) {
			if($key[0] != '_' && $key != 'id' && isset($this->$key)) $db->data[$key] = $value;
		}
		$db->Create2();
		return true;
	}

	function delete() {
		if(!isset($this->uuid)) return false;

		$db = new Crud();
		$db->deleteFrom = $this->_tablename;
		$db->condition = "uuid='". $this->uuid. "'";
		$db->Delete();
		$this->id = null;
		return true;
	}


	function update() {
		if(!isset($this->uuid)) return false;

		$db = new Crud();
		$db->update = $this->_tablename;
		foreach($this as $key => $value) {
			if($key[0] != '_' && $key != 'id' && isset($this->$key)) 
				$db->data[$key] = $value;
		}
		$db->condition = "uuid='". $this->uuid. "'";
		$db->Update2();
		return true;
	}


	public function CheckFile() {
		if (file_exists($this->_path_files. $this->uuid)) {
			$this->_message = true;
		} else {
			$this->_message = false;
		}
	}

	public function loadChild() {
		$c = new Conversion(array('files_uuid' => $this->uuid));
		$c->load('files_uuid');
		if(isset($c->id)) $this->_conversion = $c;
	}
}

class FileList {
	public $files;
	public $users_id;
	public $count;

	function __construct($record = null) {
		if(isset($record)) {
			foreach($this as $key => $value) {
				if(isset($record[$key])) $this->$key = $record[$key];
			}
		}
		$this->count = 0;
		$this->files = [];

		$db = new Crud();
		$db->select = '*';
		$db->from = 'files';
		$db->condition = array('=' => array('users_id' => $this->users_id));
		$db->orderby = array('column' => 'upload_time', 'direction' => 'DESC');
		$db->Read4();

		foreach($db->rows as $f) {
			$this->count++;
			$fm = new FileManager($f);
			$fm->loadChild();
			array_push($this->files, $fm);
		}
	}
}