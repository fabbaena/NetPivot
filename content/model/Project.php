<?php

require_once 'Crud.php';

class Project {   

    public $id;
    public $name;
    public $description;
    public $customerid;
    public $customername;
    public $createdate;
    public $updatedate;
    public $usercreate;
    public $userupdate;
    public $ip;
    public $attachment;
    public $total;
    public $opportunityid;
    private $_tablename;
            
    function __construct($record = NULL) {
        $this->_tablename = "projects";
        $this->total = 0;
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
        $newproject = new Crud();
        $newproject->insertInto = $this->_tablename;
        $newproject->data = array(
            "name" => $this->name,
            "description" => $this->description,
            "customerid"=> $this->customerid,
            "createdate"=> $this->createdate,
            "usercreate"=> $this->usercreate,
            "ip"=> $this->ip,
            "attachment"=> $this->attachment,
            "total" => $this->total,
            "opportunityid" => $this->opportunityid
        );
        $newproject->Create2("id");
        
        return $newproject->id;
      
    }

    function load($id=null) {
        if(!isset($id)) { $id = $this->id;}
        if(!isset($id)) return false;
        $conn = new Crud();
        $conn->select = "p.id as id, p.name as name, p.description as description, ".
            "p.customerid as customerid, p.createdate as createdate, ".
            "p.usercreate as usercreate, p.userupdate as userupdate, ".
            "p.ip as ip, p.total as total, p.opportunityid as opportunityid, ".
            "c.name as customername";
        $conn->from = $this->_tablename. " p, customers c";
        $conn->condition = "p.id <> 0 and p.customerid=c.id and p.id=". $id;
        $conn->Read();
        $projectresult = $conn->rows;
        $this->setData($projectresult[0]);
        return true;
    }

    function edit() {
        $valuelist = [];
        $newvalues = array(
            "name" => $this->name,
            "description" => $this->description,
            "customerid"=> $this->customerid,
            "updatedate"=> $this->updatedate,
            "userupdate"=> $this->userupdate,
            "total" => $this->total,
            "opportunityid" => $this->opportunityid
            
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
        header('location:../dashboard/Project/');
        return true;
    }
}

class ProjectList {

    public $project_list;
    public $projects;
    private $_tablename;

    function __construct($records = null) {
        $this->_tablename = "projects";
        $this->project_list = array();
        $this->projects = array();
        $usercreate = "";
        $cid = "";
        if(isset($records)) {
            if(isset($records['usercreate'])) {
                $usercreate = " and p.usercreate=". $records['usercreate'];
            }
            if(isset($records['customerid'])) {
                $cid = " and p.customerid=". $records['customerid'];
            }
        }
        $conn = new Crud();
        $conn->select = "p.id as id, p.name as name, p.description as description, ".
            "p.customerid as customerid, p.total as total, c.name as customername";
        $conn->from = $this->_tablename. " p, customers c";
        $conn->condition = "p.id <> 0 and p.customerid=c.id". $usercreate. $cid;
        $conn->Read();
        $projectresult = $conn->rows;
        foreach ($projectresult as $record) {
            $project = new Project($record);
            $this->addProject($project);
        }
    }

    function addProject($project) {
        array_push($this->project_list, $project->name);
        $this->projects[$project->name] = $project;
    }

}
?>

