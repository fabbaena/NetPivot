<?php

require_once 'Crud.php';

class Customer {

    public $id;
    public $name;
    public $phone;
    public $createdate;
    public $updatedate;
    public $usercreate;
    public $userupdate;
    public $ip;
    private $_tablename;

    function __construct($record = NULL) {
        $this->_tablename = "customers";
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
        $newcustomer = new Crud();
        $newcustomer->insertInto = $this->_tablename;
        $newcustomer->data = array(
            "name" => $this->name,
            "phone" => $this->phone,
            "usercreate" => $this->usercreate,
            "createdate" => $this->createdate,
            "ip" => $this->ip
        );
        $newcustomer->Create2("id");
        return $newcustomer->id;
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
        $valuelist = [];
        $newvalues = array(
            "name" => $this->name,
            "phone" => $this->phone,
            "userupdate" => $this->userupdate,
            "updatedate" => $this->updatedate
        );
        foreach ($newvalues as $key => $value) {
            array_push($valuelist, "$key='$value'");
        }
        $conn = new Crud();
        $conn->update = $this->_tablename;
        $conn->set = implode(", ", $valuelist);
        $conn->condition = "id=" . $this->id;
        $conn->Update();
    }

    function delete() {
        if(!isset($this->id)) return false;
        $db = new Crud();
        $db->deleteFrom = $this->_tablename;
        $db->condition = "id=" . $this->id;
        $db->Delete();
        return true;
    }
}

class CustomerList {

    public $customer_list;
    public $Customers;
    private $_tablename;

    function __construct($record = null) {
        $this->_tablename = "customers";
        $usercreate = "";
        if(isset($record) && isset($record['usercreate'])) {
            $usercreate = " and usercreate=".$record['usercreate'];
        }
        $this->Customers = array();
        $this->customer_list = array();
        $conn = new Crud();
        $conn->select = "*";
        $conn->from = $this->_tablename;
        $conn->condition = "id <> 0". $usercreate;
        $conn->orderby = array(
            "column" => "id",
            "direction" => "DESC"
        );
        $conn->Read();
        $customerresult = $conn->rows;
        foreach ($customerresult as $record) {
            $customer = new Customer($record);
            $this->addCustomer($customer);
        }
    }

    function addCustomer($customer) {
        array_push($this->customer_list, $customer->name);
        $this->Customers[$customer->name] = $customer;
    }

}
?>

