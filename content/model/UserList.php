<?php

require_once 'Crud.php';
require_once dirname(__FILE__) .'/../engine/functions.php';

class Role {
	public $id;
	public $name;
	public $starturl;

	function __construct($record = null) {
		if(!isset($record)) return;
		foreach($this as $attrname => $attrvalue) {
			if(isset($record[$attrname])) 
				$this->$attrname = $record[$attrname];
		}
	}
}

class RoleList {
	public $rolenames_list;
	public $roles;

	function __construct() {
		$this->rolenames_list = [];
		$this->roles = [];
		$conn = new Crud();
		$conn->select = "id, name, starturl";
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
	public $company_id;

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
		array_push($this->roles, $role);
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
        	if($key != 'roles' && 
        			$key != "used_files" && 
        			$key != "used_conversions" && 
        			isset($value)) {
        		$model->data[$key] = $value;
        	}
        }

        $model->data['password'] = isset($this->password) ? 
        	password_hash($this->password, PASSWORD_BCRYPT) :
			"PasswordNotSet";
        $model->Create2("id");
        $mensaje = $model->mensaje;
        $user_id = $model->id;
        $this->id = $model->id;

        foreach($this->roles as $role) {
	        $newrole = new Crud();
	        $newrole->insertInto = 'user_role';
	        $newrole->data = array(
	        	"user_id" => $user_id,
	        	"role_id" => $role->id);
	        $newrole->Create2();
        }
        if($ajax) return;
        if ($mensaje == true) {
            header ('location:../admin/admin_users.php?new_done');
        } else {
            header ('location:../admin/admin_users.php?new_error');
        }    
	}

	function setPassword() {
		$db = new Crud();
	    $db->update = "users";
	    $db->data = array(
	        'password' => password_hash($this->password, PASSWORD_BCRYPT),
	        'validation_string' => '');
	    $db->condition = new Condition( new Column('id'), '=', new Value($this->id));
	    $db->Update3();
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
		if(!isset($conn->rows[0])) return false;
		$this->setData($userresult[0]);

		$conn2 = new Crud();
		$conn2->select = "roleid as id, rolename as name, starturl";
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
		return true;
	}
	function load2($cols) {
		if(!is_array($cols)) return false;

		$conn = new Crud();
		$conn->select = "*";
		$conn->from = "users";

		$first = true;
		foreach($cols as $col) {
			if(!isset($this->$col)) throw new Exception("Column $col doesn't exist");
			if($first) $first = false;
			else {
				$c = new Condition($c, 'and', new Condition(new Column($col), '=', new Value($this->$col)));
			}
			$c = new Condition(new Column($col), '=', new Value($this->$col));
		}
		$conn->condition = &$c;
		if($conn->Read5() > 1) return false;
		$userresult = $conn->rows;
		if(!isset($conn->rows[0])) return false;
		$this->setData($userresult[0]);

		$conn2 = new Crud();
		$conn2->select = "roleid as id, rolename as name, starturl";
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
		return true;
	}

	function delete() {
		if(!isset($this->id)) return false;
		$db = new Crud();
		$db->deleteFrom = 'users';
		$db->condition = "id=". $this->id;
		$db->Delete();
		return true;
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

	public function login($username, $password) {
		$db = new Crud();
		$db->select = "*";
		$db->from = "users";
		$db->condition = new Condition(new Column('name'), '=', new Value($username));
		
		if($db->Read5() == 0) {
			error_log("User doesn't exist $username");
			return false;
		}

		$userdata = $db->rows[0];
		if (! password_verify($password, $userdata['password'])) {
			$this->id = 0;
			$this->company_id = 0;
			error_log("Password incorrect for user $username");
			return false;
		}
		foreach ($this as $key => $value) {
			if(isset($userdata[$key])) $this->$key = $userdata[$key];
		}
		$db->select = "roleid as id, rolename as name, starturl";
		$db->from = "user_role_view";
		$db->condition = new Condition(new Column('userid'), '=', new Value($this->id));
		$db->Read5();
		foreach($db->rows as $rolerecord) {
			$role = new Role($rolerecord);
			$this->addRole($role);
		}
		return true;
	}

	public function has_role($role) {
		if(!isset($this->roles)) return false;
		foreach($this->roles as $userrole) {
			if($role == $userrole->name) return true;
		} 
		return false;
	}

	function new_token() {
		$this->validation_string = RandomString();

		$db = new Crud();
		$db->update = "users";
		$db->data = array('validation_string' => $this->validation_string);
		$db->condition = new Condition(new Column('id'), '=', new Value($this->id));
		$db->Update3();
		return $this->validation_string;
	}


	public function valid_token() {
		if(!isset($this->email) || !isset($this->validation_string))
			return false;
	    $db = new Crud();
	    $db->select = "id";
	    $db->from = "users";
	    $db->condition = new Condition(
	    	new Condition(
	    		new Column('email'), 
	    		'=', 
	    		new Value($this->email)
	    		), 
	    	'and', new Condition(
	    		new Column('validation_string'), 
	    		'=', 
	    		new Value($this->validation_string)
	    		)
	    	);
	    $db->Read5();
	    if(isset($db->rows[0])) {
	        $this->id = $db->rows[0]['id'];
	        return true;
	    }
	    return false;

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
			$conn2->select = "roleid as id, rolename as name, starturl";
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

class UserEventList {
	public $company;
	public $count;
	public $userlist;

	function __construct($company, $term) {
		$this->company = $company;
		$conn = new Crud();
		$conn->select = "id, name";
		$conn->from = "users";
		$conn->condition = new Condition(
			new Condition(new Column('company_id'), '=', new Value($this->company)), 
			"and", 
			new Condition(new Column('name'), 'like', new Value($term. "%"))
			);
		$conn->orderby["column"] = "name";
		$conn->orderby["direction"] = "ASC";
		$this->count = $conn->Read5();

		$this->userlist = array();
		foreach($conn->rows as $f) {
			array_push($this->userlist, array("id" => $f["id"], "label" => $f["name"]));
		}

	}
}

?>