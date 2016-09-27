<?php

include_once '../model/Crud.php';

class Role {
	public $id;
	public $name;
	public $starturl;

	function __construct($record) {
		$this->id = $record["roleid"];
		$this->name = $record["rolename"];
		$this->starturl = $record["starturl"];
	}
}

class RoleList {
	public $rolenames_list;
	public $roles;

	function __construct() {
		$this->rolenames_list = [];
		$this->roles = [];
		$conn = new Crud();
		$conn->select = "id as roleid, name as rolename, starturl";
		$conn->from = "roles";
		$conn->Read();
		$result = $conn->rows;
		foreach($result as $record) {
			$role = new Role($record);
			array_push($this->rolenames_list, $role->name);
			array_push($this->roles, $role);
		}
	}
}

class User {
	public $id;
	public $name;
	public $type;
	public $max_files;
	public $max_conversions;
	public $used_files;
	public $used_conversions;
	public $roles;
	public $password;
	public $email;
	public $validation_string;
	public $company;
	public $position;
	public $firstname;
	public $lastname;

	function __construct($record = NULL) {
		$this->roles = [];
		if($record != NULL) {
			foreach($this as $key => $value) {
				if($key != "roles" && isset($record[$key])) {
					$this->$key = $record[$key];
				}
			}
		}
	}

	function setData($record) {
		foreach($this as $key => $value) {
			if($key != "roles" && isset($record[$key])) {
				$this->$key = $record[$key];
			}
		}
	}

	function addRole($role) {
		$this->roles[$role->id] = $role;
	}

	function rmRole($id) {
		unset($this->roles[$id]);
	}

	function getMaxFiles() {
		return $this->max_files==0?"Unlimited":$this->max_files;
	}
	function getMaxConversions() {
		return $this->max_conversions==0?"Unlimited":$this->max_conversions;
	}
	function getRoleList() {
		$out = [];
		foreach($this->roles as $role) {
			array_push($out, $role->name);
		}
		return $out;
	}
	function save($ajax = false) {
		$model = new Crud();
        $model->insertInto = 'users';
        foreach($this as $key => $value) {
        	if($key != 'roles' && isset($value)) {
        		$model->data[$key] = $value;
        	}
        }
        $model->data['password'] = isset($this->password) ? 
        	password_hash($this->password, PASSWORD_BCRYPT) :
			"PasswordNotSet";
        $model->Create2("id");
        $mensaje = $model->mensaje;
        $user_id = $model->id;

        foreach($this->roles as $role_id => $role) {
	        $newrole = new Crud();
	        $newrole->insertInto = 'user_role';
	        $newrole->data = array(
	        	"user_id" => $user_id,
	        	"role_id" => $role_id);
	        $newrole->Create2();
        }
        if($ajax) return;
        if ($mensaje == true) {
            header ('location:../admin/admin_users.php?new_done');
        } else {
            header ('location:../admin/admin_users.php?new_error');
        }    
	}
	function load() {
		$conn = new Crud();
		$conn->select = "*";
		$conn->from = "users";
		if(isset($this->name)) {
			$conn->condition = "name='". $this->name. "'";
		} elseif(isset($this->id)){
			$conn->condition = "id=". $this->id;
		}
		$conn->Read();
		$userresult = $conn->rows;
		$this->setData($userresult[0]);

		$conn2 = new Crud();
		$conn2->select = "*";
		$conn2->from = "user_role_view";
		$conn2->condition = "userid=". $this->id;
		$conn2->Read();
		$roleresult = $conn2->rows;
		if($roleresult) {
			foreach($roleresult as $rolerecord) {
				$role = new Role($rolerecord);
				$this->addRole($role);
			}
		}
	}
	function modify($newvalues) {
		if(isset($newvalues)) {
			$valuelist = [];
			if(isset($newvalues["roles"])) {
				$roles = $newvalues["roles"];
				unset($newvalues["roles"]);
			}
			if(isset($newvalues["password"])) {
				$newvalues["password"] = password_hash($newvalues["password"], PASSWORD_BCRYPT);
			}
			foreach($newvalues as $key =>$value) {
				if($this->$key != $value) {
					array_push($valuelist, "$key='$value'");
				}
			}
			if(count($valuelist) > 0) {
				$conn = new Crud();
				$conn->update = "users";
				$conn->set = implode(", ", $valuelist);
				$conn->condition = "id=". $this->id;
				$conn->Update();
			}
			if(isset($roles)) {
				foreach ($roles as $role => $state) {
					if(!isset($this->roles[$role]) && $state=="true") {
						$columns = ['user_id', 'role_id'];
						$values = [$this->id, $role];
				        $newrole = new Crud();
				        $newrole->insertInto = 'user_role';
				        $newrole->insertColumns = implode(',', $columns);
				        $newrole->insertValues = implode(',', $values);
				        $newrole->Create();
					} else if(isset($this->roles[$role]) && $state=="false") {
						$user_id = $this->id;
						$role_id = $role;
				        $newrole = new Crud();
				        $newrole->deleteFrom = 'user_role';
				        $newrole->condition = "user_id=$user_id and role_id=$role_id";
				        $newrole->Delete();
					}
				}
			}
		}
	}
}

class UserList {
	public $username_list;
	public $users;

	function __construct() {
		$this->username_list = [];
		$conn = new Crud();
		$conn->select = "*";
		$conn->from = "users";
		$conn->Read();
		$userresult = $conn->rows;
		foreach($userresult as $record) {
			$user = new User($record);
			$conn2 = new Crud();
			$conn2->select = "*";
			$conn2->from = "user_role_view";
			$conn2->condition = "userid=". $user->id;
			$conn2->Read();
			$roleresult = $conn2->rows;
			if($roleresult) {
				foreach($roleresult as $rolerecord) {
					$role = new Role($rolerecord);
					$user->addRole($role);
				}
			}
			$this->addUser($user);
		}

	}

	function rmUser($username) {
		unset($this->users[$username]);
		unset($this->username_list[array_search($username, $this->username_list)]);
	}

	function addUser($user) {
		array_push($this->username_list, $user->name);
		$this->users[$user->name] = $user;
	}

	function getUser($data) {
		return $this->users[$name];
	}
}
?>