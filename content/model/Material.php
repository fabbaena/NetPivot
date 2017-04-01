<?php

require_once 'Crud.php';

class Material {

    public $id;
    public $sku;
    public $description;
    public $quantity;
    public $price;
    public $projectid;
    private $_tablename;

    function __construct($record = NULL) {
        $this->_tablename = "billofmaterials";
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
        $newmaterial = new Crud();
        $newmaterial->insertInto = $this->_tablename;
        $newmaterial->data = array(
            "sku" => $this->sku,
            "description" => $this->description,
            "quantity"=> $this->quantity,
            "price"=> $this->price,
            "projectid"=> $this->projectid
        );
        
        $newmaterial->Create2();
        return $newmaterial->mensaje;
    }

    function load() {
        $conn = new Crud();
        $conn->select = "*";
        $conn->from = $this->_tablename;
        $conn->condition = "id=" . $this->id;
        $conn->Read();
        $customerresult = $conn->rows;
        $this->setData($customerresult[0]);
        return true;
    }

    function edit() {
        $valuelist = [];

        foreach ($this as $key => $value) {
            if($key[0] != '_' && $key != 'id') {
                array_push($valuelist, "$key='$value'");
            }
        }
        $conn = new Crud();
        $conn->update = $this->_tablename;
        $conn->set = implode(", ", $valuelist);
        $conn->condition = "id=" . $this->id;
        $conn->Update();
        return $conn->mensaje;
    }

    function delete() {
        $db = new Crud();
        $db->deleteFrom = $this->_tablename;
        $db->condition = "id=" . $this->id;
        $db->Delete();
        return $db->mensaje;
    }

}
class MaterialList {
    

    public $material_list;
    public $Materials;
    private $_tablename;

    function __construct($projectid) {
        $this->_tablename = "billofmaterials";
        $this->material_list = array();
        $this->Materials = array();
        $conn = new Crud();
        $conn->select = "*";
        $conn->from = $this->_tablename;
        $conn->condition = "projectid=".$projectid;
        $conn->Read();
        $materialresult = $conn->rows;
        foreach ($materialresult as $record) {
            $material = new Material($record);
            $this->addMaterial($material);
        }
    }

    function addMaterial($material) {
        array_push($this->material_list, $material->id);
        $this->Materials[$material->id] = $material;
    }

}

?>

