<?php

class Domain {
	public $id;
	public $name;
	public $domain;

	function __construct($record = null) {
		if(isset($record)) {
			if(isset($record['id']))
				$this->id = $record['id'];
			if(isset($record['name']))
				$this->name = $record['name'];
			if(isset($record['domain']))
				$this->domain = $record['domain'];
		}
	}

	function save() {
		$model = new Crud();
		$model->insertInto = 'companies';
		$model->insertColumns = 'name, domain';
		$model->insertValues = "'". $this->name. "', '". $this->domain. "'";
		$model->create();
	}

	function load() {
		$model = new Crud();
		$model->select = "id, name, domain";
		$model->from = "companies";
		if(isset($this->name)) {
			$model->condition = "name='". $this->name. "'";
		} elseif(isset($this->domain)) {
			$model->condition = "domain='". $this->domain. "'";
		} elseif(isset($this->id)) {
			$model->condition = "id=". $this->id;
		}
		$model->Read();
		$domainresult = $model->rows;
		if(!isset($domainresult[0])) return false;
		$this->name = $domainresult[0]['name'];
		$this->domain = $domainresult[0]['domain'];
		$this->id = $domainresult[0]['id'];
		return true;
	}

	function delete() {
		if(isset($this->id)) {
			$model = new Crud();
			$model->deleteFrom = "companies";
			$model->condition = "id=". $this->id;
			$model->Delete();
			return true;
		}
		return false;
	}

	function modify() {
		$model = new Crud();
		$model->update = "companies";
		$model->set = "name='". $this->name. "', domain='". $this->domain. "'";
		$model->condition = "id=". $this->id;
		$model->Update();
	}
}

class DomainList {
	public $list;

	function __construct() {
		$this->domain_list = [];
		$model = new Crud();
		$model->select = "id, name, domain";
		$model->from = "companies";
		$model->condition = "id <> 0";
		$model->Read();
		$domainresult = $model->rows;
		foreach ($domainresult as $record) {
			$domain = new Domain($record);
			$this->list[$domain->name] = $domain;
		}
	}
}
?>