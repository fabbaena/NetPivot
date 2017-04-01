<?php
require_once 'Crud.php';

class Tip {

    public $id;
    public $name;
    public $description;   
    public $createdate;
    public $updatedate;
    public $usercreate;
    public $userupdate;
    public $ip;
    private $_tablename;

    function __construct($record = NULL) {
        $this->_tablename = "tips";
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
        $newtip = new Crud();
        $newtip->insertInto = $this->_tablename;
        $newtip->data = array(
            "name" => $this->name,
            "description" => $this->description,
            "createdate"=> $this->createdate,
            "usercreate"=> $this->usercreate,
            "ip"=> $this->ip
        );
       
        return $newtip->Create2();

    }

    function load($id) {
        $conn = new Crud();
        $conn->select = "*";
        $conn->from = $this->_tablename;
        $conn->condition = "id=" . $this->id;
        $conn->Read();
        $tipresult = $conn->rows;
        $this->setData($tipresult[0]);
        return true;
    }

    function edit() {
        $valuelist = [];
        $newvalues = array(
            "name" => $this->name,
            "description" => $this->description,
            "updatedate"=> $this->updatedate,
            "userupdate"=> $this->userupdate
            
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
        $db = new Crud();
        $db->deleteFrom = $this->_tablename;
        $db->condition = "id=" . $this->id;
        $db->Delete();
        header('location:../dashboard/Tips/default.php');
        return true;
    }

}

class TipList {

    public $tip_list;
    public $Tips;

    function __construct() {
        $conn = new Crud();
        $conn->select = "*";
        $conn->from = $this->_tablename;
        $conn->condition = "id <> 0";
      
        $conn->Read();
        $tipresult = $conn->rows;
        foreach ($tipresult as $record) {
            $tip = new Tip($record);
            $this->addTip($tip);
        }
    }

    function addTip($tip) {
        array_push($this->tip_list, $tip->name);
        $this->Tips[$tip->name] = $tip;
    }

}
?>

