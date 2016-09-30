<?php

class Domain {
	public $id;
	public $name;

	function __construct($record = null) {
		if(isset($record)) {
			if(isset($record['id']))
				$this->id = $record['id'];
			if(isset($record['name']))
				$this->name = $record['name'];
		}
	}

	function save() {
		$model = new Crud();
		$model->insertInto = 'domains';
		$model->insertColumns = 'name';
		$model->insertValues = "'". $this->name. "'";
		$model->create();
	}

	function load() {
		$model = new Crud();
		$model->select = "id, name";
		$model->from = "domains";
		if(isset($this->name)) {
			$model->condition = "name='". $this->name. "'";
		} elseif(isset($this->id)) {
			$model->condition = "id=". $this->id;
		}
		$model->Read();
		$domainresult = $model->rows;
		if(!isset($domainresult[0])) return false;
		$this->name = $domainresult[0]['name'];
		$this->id = $domainresult[0]['id'];
		return true;
	}

	function delete() {
		if(isset($this->id)) {
			$model = new Crud();
			$model->deleteFrom = "domains";
			$model->condition = "id=". $this->id;
			$model->Delete();
			return true;
		}
		return false;
	}

	function modify() {
		$model = new Crud();
		$model->update = "domains";
		$model->set = "name='". $this->name. "'";
		$model->condition = "id=". $this->id;
		$model->Update();
	}
}

class DomainList {
	public $list;

	function __construct() {
		$this->domain_list = [];
		$model = new Crud();
		$model->select = "id, name";
		$model->from = "domains";
		$model->Read();
		$domainresult = $model->rows;
		foreach ($domainresult as $record) {
			$domain = new Domain($record);
			$this->list[$domain->name] = $domain;
		}
	}
}
?>