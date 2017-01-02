<?php
require_once 'Crud.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../engine/functions.php';

class Event {
	public $timestamp;
	public $company_id;
	public $user_id;
	public $event;
	public $event_code;

	/*
	Event_Code:
	1 = Login
	2 = Logout
	3 = View File
	4 = Download File
	5 = Register
	6 = File Uploaded
	7 = File Converted
	8 = Stats Generated
	*/

	function __construct(&$user = null, $e = null, $c = null) {

		$this->user_id = isset($user) ? $user->id : 0;
		$this->company_id = isset($user) ? $user->company_id : 0;
		$this->timestamp = date("c");
		$this->event = $e;
		$this->event_code = $c;
		$this->save();
	}

	function save() {
		$model = new Crud();
        $model->insertInto = 'events';
        foreach($this as $key => $value) {
    		$model->data[$key] = $value;
        }
        $model->Create2("id");
        $mensaje = $model->mensaje;
        $user_id = $model->id;

	}
}

class EventRead extends Event {
	public $company_name;
	public $user_fullname;
	public $user_name;
	public $event_id;

	function __construct(&$record = null) {
		if(is_array($record)) {
			foreach($this as $key => $value) {
				if(isset($record[$key])) $this->$key = $record[$key];
			}
		}
	}

}

class EventList {
	public $user_id;
	public $company_id;
	public $events;
	public $count;
	public $oldest_timestamp;
	public $newest_timestamp;
	public $event_code;

	function __construct($records = null) {
		if(is_array($records)) {
			foreach($this as $key => $value) {
				if(isset($records[$key])) $this->$key = $records[$key];
			}
		}
		$this->count = 0;
		$this->events = [];

		$db = new Crud();
		$db->select = '*';
		$db->from = 'events_full';
		if(isset($this->user_id)) {
			$c = new Condition(new Column('user_id'), '=', new Value($this->user_id));
		}
		if(isset($this->company_id)) {
			if(isset($c)) {
				$c = new Condition($c, 'and', new Condition(new Column('company_id'), '=', new Value($this->company_id)));
			} else {
				$c = new Condition(new Column('company_id'), '=', new Value($this->company_id));
			}
		} 
		if(isset($this->oldest_timestamp)) {
			$c = new Condition($c, 'and', new Condition(new Column('timestamp'), '>=', new Value($this->oldest_timestamp)));
		}
		if(isset($this->newest_timestamp)) {
			$c = new Condition($c, 'and', new Condition(new Column('timestamp'), '<=', new Value($this->newest_timestamp)));
		}
		if(isset($this->event_code)) {
			$c = new Condition($c, 'and', new Condition(new Column('event_code'), '=', new Value($this->event_code)));
		}

		$db->condition = $c;
		$db->orderby = array('column' => 'timestamp', 'direction' => 'DESC');
		$this->count = $db->Read5();

		foreach($db->rows as $f) {
			$e = new EventRead($f);
			array_push($this->events, $e);
		}
	}

}

?>