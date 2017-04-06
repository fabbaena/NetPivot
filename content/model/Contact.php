<?php

require_once 'Crud.php';

class Contact {

    public $id;
    public $name;
    public $position;
    public $phone;
    public $createdate;
    public $updatedate;
    public $usercreate;
    public $userupdate;
    public $ip;
    public $customerid;
    private $_tablename;

    function __construct($record = NULL) {
        $this->_tablename = "contacts";
        if ($record != NULL) {
            foreach ($this as $key => $value) {
                if ($key != "roles" && isset($record[$key])) {
                    $this->$key = $record[$key];
                }
            }
        }
    }

    function setData($record) {
        foreach ($this as $key => $value) {
            if (isset($record[$key])) {
                $this->$key = $record[$key];
            }
        }
    }

    function save($ajax = false) {
        $newcontactcustomer = new Crud();
        $newcontactcustomer->insertInto = $this->_tablename;
        $newcontactcustomer->data = array(
            "name" => $this->name,
            "position" => $this->position,
            "phone"=> $this->phone,
            "customerid"=> $this->customerid,
            "usercreate"=> $this->usercreate,
            "createdate"=> $this->createdate,
            "ip"=> $this->ip
        );      
        $newcontactcustomer->Create2("id");
        $this->id = $newcontactcustomer->id;
     
        return $this->id;
    }

    function load($id=null) {
        if(!isset($id)) { $id = $this->id;}
        if(!isset($id)) return false;
        $conn = new Crud();
        $conn->select = "*";
        $conn->from = $this->_tablename;
        $conn->condition = "id=" . $id;
        $conn->Read();
        $customerresult = $conn->rows;
        $this->setData($customerresult[0]);
        return true;
    }

    function edit() {
        $conn = new Crud();
        $conn->data = array(
            "name" => $this->name,
            "position" => $this->position,
            "phone"=> $this->phone,
            "userupdate"=> $this->userupdate,
            "updatedate"=> $this->updatedate,
            "customerid"=>$this->customerid
        );
        $conn->update = $this->_tablename;
        $conn->condition = "id=" . $this->id;
        $conn->Update2();
    }

    function delete() {
        $db = new Crud();
        $db->deleteFrom = $this->_tablename;
        $db->condition = "id=" . $this->id;
        $db->Delete();
        return true;
    }

}

class ContactList {
    

    public $contact_list;
    public $Contacts;
    private $_tablename = "contacts";

    function __construct($data) {
        $filter = "";
        if(isset($data['usercreate'])) {
            $filter = "usercreate=". $data['usercreate'];
        }
        if(isset($data['customerid'])) {
            if(strlen($filter) > 0) $filter .= ' and ';
            $filter .= "customerid=". $data['customerid'];
        }

        $this->Contacts = array();
        $this->contact_list = array();
        $conn = new Crud();
        $conn->select = "*";
        $conn->from = $this->_tablename;
        $conn->condition = $filter;
        $conn->Read();
        $contactresult = $conn->rows;
        foreach ($contactresult as $record) {
            $contact = new Contact($record);
            $this->addContact($contact);
        }
    }

    function addContact($contact) {
        array_push($this->contact_list, $contact->name);
        $this->Contacts[$contact->name] = $contact;
    }

}
?>

