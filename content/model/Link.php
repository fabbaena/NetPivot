<?php
require_once 'Crud.php';

class Link {

    public $f5;
    public $ns;
    public $files_uuid;
    public $f5start;
    public $f5end;
    private $_tablename;

    function __construct($record = NULL) {
        $this->_tablename = "f5nslink";
        $this->ns = array();
        if ($record != NULL) {
            foreach ($this as $key => $value) {
                if (isset($record[$key])) {
                    if($key == "ns") {
                        array_push($this->ns, $record[$key]);
                    } else {
                        $this->$key = $record[$key];
                    }
                }
            }
        }
    }

    function setData($record) {
        foreach ($this as $key => $value) {
            if (isset($record[$key])) {
                if($key == "ns") {
                    array_push($this->ns, $record[$key]);
                } else {
                    $this->$key = $record[$key];
                }
            }
        }
    }

    function save() {
        $newlink = new Crud();
        $newlink->insertInto = $this->_tablename;
        $newlink->CreateBulk(true, array("f5", "ns", "files_uuid"));
        foreach($this->ns as $nslink) {
            if($nslink == "") continue;
            $newlink->data = array($this->f5, $nslink, $this->files_uuid);
            if($newlink->CreateBulk() === false) {
                return $newlink->mensaje;
            }
        }
        return true;

    }

    function load() {
        if(!isset($this->files_uuid)) return false;
        if(!isset($this->f5) && !(isset($this->f5start) && isset($this->f5end))) return false;
        $conn = new Crud();
        $conn->select = "*";
        $conn->from = $this->_tablename;
        $conn->condition = "files_uuid='". $this->files_uuid. "'";
        if(isset($this->f5)) {
            $conn->condition .= " and f5=" . $this->f5;
        } else if(isset($this->f5start) && isset($this->f5end)) {
            $conn->condition .= " and f5 >= ". $this->f5start. " and f5 <= ". $this->f5end;
        } else return false;
        $conn->orderby = " ORDER BY ns ASC";
        $conn->Read();
        $linkresult = $conn->rows;
        foreach($linkresult as $l) {
            array_push($this->ns, $l['ns']);
        }
        return true;
    }

}

?>

