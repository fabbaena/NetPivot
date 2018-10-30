<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * This class is meant to be the CRUD for the postgres database.
 *
 * @author gonzalorodriguez
 */
class Crud {
    public $insertInto;
    public $insertColumns;
    public $insertValues;
    public $select;
    public $from;
    public $mensaje;
    public $rows;
    public $condition;
    public $update;
    public $set;
    public $deleteFrom;
    public $tabla2;
    public $id;
    public $filename;
    public $uuid;
    public $fetchall;
    public $data;               // 
    public $orderby;
    public $groupby;

    private $_conn;             // PDO object for database connection
    private $_req;              // Only used in CreateBulk() (possibly a pdo)
    

    function __construct() {
        $usuario = "demonio";
        $pass = "s3cur3s0c";
        $host = "localhost";
        $db = "netpivot";
        $this->_conn = new PDO("pgsql:host=$host;dbname=$db", $usuario, $pass);
        $this->_req = false;
    }
    
    /**
     * Parameters used:
     *      - $this->insertInto
     *      - $this->insertColumns
     *      - $this->insertValues
     * 
     * Parameters with side-effects:
     *      - $this->mensaje
     *      - $this->id (if prearing npo returns something a non-trivial statement)
     */
    public function Create(){
        $insertInto = $this->insertInto;
        $insertColumns = $this->insertColumns;
        $insertValues = $this->insertValues;
        $sql = "INSERT INTO $insertInto ($insertColumns) VALUES ($insertValues)";
        $consulta = $this->_conn->prepare($sql);
        if (!$consulta){
            $this->mensaje = errorInfo();
        } else {
            $consulta->execute();
            $this->mensaje = TRUE;
            $this->id = $this->_conn->lastInsertId();
        }  
    }

    /**
     * Parameters used:
     *      - $this->insertInto
     *      - $this->data
     * Parameters with side-effects:
     *      - $this->mensaje
     *      - $this->id (if prearing npo returns something a non-trivial statement)
     */
    public function Create2($returning = null) {
        $insertColumnsArray = array();
        $insertValuesArray = array();
        $insertValuesQArray = array();

        $insertInto = $this->insertInto;

        foreach($this->data as $column => $value) {
            if(!is_numeric($column)) {
                array_push($insertColumnsArray, $column);
                array_push($insertValuesArray, $value);
                array_push($insertValuesQArray, "?");
            }
        }
        $insertColumns = implode(",", $insertColumnsArray);
        $insertValues  = implode(",", $insertValuesQArray);
        $sql = "INSERT INTO $insertInto ($insertColumns) VALUES ($insertValues)";
        if(isset($returning)) $sql .= " RETURNING $returning";
        $consulta = $this->_conn->prepare($sql);
        if (!$consulta){
            $this->mensaje = errorInfo();
        } else {
            $consulta->execute($insertValuesArray);
            $this->id = isset($returning)?$consulta->fetch()["id"]:null;
            $this->mensaje = TRUE;
        }  
    }

    /**
     * Parameters used:
     *      - $this->insertInto
     *      - $this->_req (if $prepare is false or nothing )
     * Parameters with side-effects:
     *      - $this->_req (if prepare is not false)
     *      - $this->mensaje (if $prepare is false or nothing, and 
     *          $this->_req does not have the value 'false')
     */
    public function CreateBulk($prepare = false, $insertColumnsArray = array()) {
        if($prepare) {
            $insertInto = $this->insertInto;
            $insertValuesArray = array();
            foreach($insertColumnsArray as $i) {
                array_push($insertValuesArray, "?");
            }
            $insertColumns = implode(",", $insertColumnsArray);
            $insertValues = implode(",", $insertValuesArray);
            $sql = "INSERT INTO $insertInto ($insertColumns) VALUES ($insertValues)";
            $this->_req = $this->_conn->prepare($sql);
        } else if($this->_req !== false) {
            $out = $this->_req->execute($this->data);
            if($out === false) {
                $this->mensaje = $this->_conn->errorInfo()[2];
            }
            return $out;
        }
    }
    
    /**
     * Parameters used:
     *      - $this->select
     *      - $this->from
     *      - $this->condition
     *      - $this->orderby
     *      - $this->groupby
     * Parameters with side-effects:
     *      - $this->rows
     */
    public function Read(){
        $select = $this->select;
        $from = $this->from;
        $condition = $this->condition;
        $orderby = $this->orderby;
        if ( $condition != ''){
            $condition = " WHERE " .$condition;
        }
        $groupby = $this->groupby;
        if($groupby != '') {
            $groupby = " GROUP BY ". $groupby;
        }
        $sql = "SELECT $select FROM $from $condition $groupby$orderby";
        $consulta = $this->_conn->prepare($sql);
        $consulta->execute();
        $this->rows = [];
        while ($filas = $consulta->fetch()){
            $this->rows[] = $filas;
        }

    }

    /**
     * Parameters used:
     *      - $this->select
     *      - $this->from
     *      - $this->condition
     *      - $this->groupby
     * Parameters with side-effects:
     *      - $this->rows
     */
    public function ReadObject(){
        $select = $this->select;
        $from = $this->from;
        $condition = $this->condition;
        if ( $condition != ''){
            $condition = " WHERE " .$condition;
        }
        $groupby = $this->groupby;
        if($groupby != '') {
            $groupby = " GROUP BY ". $groupby;
        }
        $sql = "SELECT $select FROM $from $condition $groupby";
        $consulta = $this->_conn->prepare($sql);
        $consulta->execute();
        $this->rows = [];
        while ($fila = $consulta->fetchObject()){
            array_push($this->rows, $fila);
        }

    }

    /**
     * Parameters used:
     *      - $this->select
     *      - $this->from
     *      - $this->condition
     * Parameters with side-effects:
     *      - $this->fetchall
     */
    public function Read2(){
        $select = $this->select;
        $from = $this->from;
        $condition = $this->condition;
        if ( $condition != ''){
            $condition = " WHERE " .$condition;
        }
        $sql = "SELECT $select FROM $from $condition";
        $consulta = $this->_conn->prepare($sql);
        $consulta->execute();
        $this->fetchall = $consulta->fetchall();

    }
    

    /**
     * Parameters used:
     *      - $this->select
     *      - $this->from
     *      - $this->condition
     * Parameters with side-effects:
     *      - $this->rows
     */
    public function Read3(){
        $select = $this->select;
        $from = $this->from;
        $condition = '';
        $data = [];
        foreach($this->condition as $operator => $elements) {
            if($operator == 'and' || $operator == 'or') {
                $condition .= " $operator ";
                continue;
            }
            if(!is_array($elements)) continue;
            foreach($elements as $key => $value) {
                $condition .= "$key $operator ? ";
                array_push($data, $value);
            }
        }
        if ( $condition != ''){
            $condition = " WHERE " .$condition;
        }
        $sql = "SELECT $select FROM $from $condition";
        $consulta = $this->_conn->prepare($sql);
        $consulta->execute($data);
        $this->rows = [];
        while ($filas = $consulta->fetch()){
            $this->rows[] = $filas;
        }

    }

    /**
     * Parameters used:
     *      - $this->select
     *      - $this->from
     *      - $this->condition
     *      - $this->orderby
     * Parameters with side-effects:
     *      - $this->rows
     */
    public function Read4(){
        $select = $this->select;
        $from = $this->from;
        $condition = '';
        $orderby = '';
        $data = [];
        if(is_array($this->condition)) {
            foreach($this->condition as $bool => $elements) {
                $first = true;
                foreach($elements as $key => $e) {
                    if(is_array($e)) {
                        foreach($e as $operator => $keyval) {
                            if($first) $first = false;
                            else $condition = "$condition $bool ";

                            foreach($keyval as $key => $val) {
                                $condition = "$condition $key $operator ? ";
                                array_push($data, $val);
                            }
                        }
                    } else {
                        $operator = $bool;
                        $val = $e;
                        $condition = "$condition $key $operator ? ";
                        array_push($data, $val);
                    }
                }
            }
            $condition = " WHERE $condition";
        }
        if(is_array($this->orderby)) {
            $orderby = "ORDER BY ". $this->orderby["column"]. " ". $this->orderby["direction"];
        }
        $sql = "SELECT $select FROM $from $condition $orderby";
        $consulta = $this->_conn->prepare($sql);
        $consulta->execute($data);
        $this->rows = [];
        while ($filas = $consulta->fetch()){
            $this->rows[] = $filas;
        }

    }

    /**
     * Parameters used:
     *      - $this->select
     *      - $this->from
     *      - $this->condition
     *      - $this->orderby
     * Parameters with side-effects:
     *      - $this->data
     *      - $this->rows
     * 
     * @return Length of the $rows parameter after modifications.
     */
    public function Read5(){
        $select = $this->select;
        $from = $this->from;
        $condition = '';
        $orderby = '';
        $this->data = [];
        if(is_a($this->condition, "Condition")) {
            $condition = "WHERE ". $this->condition->toString();
        }
        $this->condition->toValue($this->data);

        if(is_array($this->orderby)) {
            $orderby = "ORDER BY ". $this->orderby["column"]. " ". $this->orderby["direction"];
        }
        $sql = "SELECT $select FROM $from $condition $orderby";
        $consulta = $this->_conn->prepare($sql);
        $consulta->execute($this->data);
        $this->rows = $consulta->fetchall();
        return count($this->rows);
    }
    
    /**
     * Parameters used:
     *      - $this->update
     *      - $this->set
     *      - $this->condition
     * Parameters with side-effects:
     *      - $this->mensaje
     */
    public function Update(){
        $update = $this->update;
        $set = $this->set;
        $condition = $this->condition;
        if ($condition != ''){
            $condition = 'WHERE ' . $condition;
        }
        $sql = "UPDATE $update set $set $condition";
        $consulta = $this->_conn->prepare($sql);
        if (!$consulta){
            $this->mensaje = errorInfo();
        } else {
            $consulta->execute();
            $this->mensaje = TRUE;
        }
    }
    
    /**
     * Parameters used:
     *      - $this->condition
     *      - $this->update
     *      - $this->data
     * Parameters with side-effects:
     *      - $this->mensaje
     */
    public function Update2() {
        $condition = $this->condition;
        $updateColumnsArray = array();
        $updateValuesArray = array();
        $updateValuesQArray = array();

        $update = $this->update;

        foreach($this->data as $column => $value) {
            if(!is_numeric($column)) {
                array_push($updateColumnsArray, "$column=?");
                array_push($updateValuesArray, $value);
            }
        }
        $updateColumns = implode(",", $updateColumnsArray);
        $sql = "UPDATE $update SET $updateColumns WHERE $condition";
        $consulta = $this->_conn->prepare($sql);
        if (!$consulta){
            $this->mensaje = errorInfo();
        } else {
            $consulta->execute($updateValuesArray);
            $this->mensaje = TRUE;
        }  
    }

    /**
     * Parameters used:
     *      - $this->update
     *      - $this->data
     *      - $this->condition
     * Parameters with side-effects:
     *      - $this->mensaje
     */
    public function Update3() {
        $condition = '';

        $updateColumnsArray = array();
        $updateValuesArray = array();
        $updateValuesQArray = array();

        $update = $this->update;

        foreach($this->data as $column => $value) {
            if(!is_numeric($column)) {
                array_push($updateColumnsArray, "$column=?");
                array_push($updateValuesArray, $value);
            }
        }
        if(is_a($this->condition, "Condition")) {
            $condition = "WHERE ". $this->condition->toString();
        } 
        $this->condition->toValue($updateValuesArray);

        $updateColumns = implode(",", $updateColumnsArray);
        $sql = "UPDATE $update SET $updateColumns $condition";
        $consulta = $this->_conn->prepare($sql);
        if (!$consulta){
            $this->mensaje = errorInfo();
        } else {
            $consulta->execute($updateValuesArray);
            $this->mensaje = TRUE;
        }  
    }

    /**
     * Parameters used:
     *      - $this->deleteFrom
     *      - $this->condition
     * Parameters with side-effects:
     *      - $this->mensaje
     */
    public function Delete(){
        $deleteFrom = $this->deleteFrom;
        $condition = $this->condition;
        if($condition != ''){
            $condition = ' WHERE ' . $condition;
        }
        $sql = "DELETE FROM $deleteFrom $condition";
        $consulta = $this->_conn->prepare($sql);
        if (!$consulta) {
            $this->mensaje = errorInfo();
        } else {
            $consulta->execute();
            $this->mensaje = TRUE;
        }
    }

    /**
     * Parameters used:
     *      - $this->filename
     *      - $this->uuid
     * Parameters with side-effects:
     *      - $this->mensage
     */
    public function Load(){
        $model = new ConnectionBD();
        $conexion = $model->conectar();
        $filename = $this->filename;
        $uuid = $this->uuid;
        $sql = "load data infile '$filename' ".
                "into table details fields terminated by ',' ".
                "(module, obj_grp, obj_component, obj_name, attribute, converted, omitted, line, files_uuid) ".
                "set files_uuid=\"$uuid\";";
        $consulta = $conexion->prepare($sql);
        if (!$consulta) {
            $this->mensaje = $consulta->errorInfo();
        } else {
            $consulta->execute();
            $this->mensaje = $consulta->errorInfo();
        }
    }
    
}

        $consulta = $this->_conn->prepare('BEGIN');
        $consulta->execute();
        $sql = "INSERT INTO details (module, obj_grp, obj_component, obj_name, attribute, converted, omitted, line, files_uuid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $consulta = $this->_conn->prepare($sql);
        $csvfile = fopen($filename, "r") or die("Unable to open $filename!");
        while($line = fgets($csvfile)) {
            $arr = explode(",", $line);
            array_push($arr, $uuid);
            $consulta->execute($arr);
        }
        fclose($csvfile);
        $consulta = $this->_conn->prepare('COMMIT');
        $consulta->execute();
        $this->mensage = $consulta->errorInfo();
    }

    /**
     * Parameters used:
     *      ...parameters required in Create2() 
     * Parameters with side-effects:
     *      - $this->insertInto
     *      - $this->data
     *      ...parameters changed in Create2()
     */
    function uploadJSON($uuid, $objectgroup, $obj) {
        foreach($obj as $object) {
            if(!isset($object["name"])) continue;
            $name =  key($object);
            $v = $object[$name];
            $this->insertInto = "f5_${objectgroup}_json";

            $this->data = array(
                "files_uuid" => $uuid,
                "name"       => $name,
                "adminpart"  => $v["adminpart"],
                "attributes" => json_encode($v["attributes"]));

            if(isset($v["type"])) {
                $this->data["type"] = $v["type"];
            }
            $this->Create2();
        }
    }

    /**
     * Parameters used:
     *      - $this->id
     *      ...parameters changed in Create2()
     * Parameters with side-effects:
     *      - $this->insertInto
     *      - $this->data
     *      ...parameters changed in Create2()
     */
    function uploadJSON2($uuid, $data) {
        $stats = array();
        foreach($data as $key => $m) {
            if(!is_array($m)) continue;
            foreach($m as $key => $obj) {
                if(!is_array($obj)) continue;
                $c = 0;
                $t = 0;
                $feature = $obj["feature"];
                $module = $obj["module"];
                foreach ($obj["attributes"] as $attr => $a) {
                    if(!is_array($a)) continue;
                    $c += isset($a["converted"])?$a["converted"]:0;
                    $t += 1;
                }
                if(!isset($stats[$feature])) {
                    $stats[$feature] = array();
                    $stats[$feature]["modules"] = array();
                    $stats[$feature]["objects"] = 1;
                    $stats[$feature]["converted"] = $c;
                    $stats[$feature]["total"] = $t;
                } else {
                    $stats[$feature]["objects"] += 1;
                    $stats[$feature]["converted"] += $c;
                    $stats[$feature]["total"] += $t;
                }

                if(!isset($stats[$feature]["modules"][$module])) {
                    $stats[$feature]["modules"][$module] = array();
                    $stats[$feature]["modules"][$module]["objects"] = 1;
                    $stats[$feature]["modules"][$module]["converted"] = $c;
                    $stats[$feature]["modules"][$module]["total"] = $t;
                } else {
                    $stats[$feature]["modules"][$module]["objects"] += 1;
                    $stats[$feature]["modules"][$module]["converted"] += $c;
                    $stats[$feature]["modules"][$module]["total"] += $t;
                }

                $this->insertInto = "f5_attributes_json";
                $this->data = $obj;
                $attributes = $this->data["attributes"];
                unset($this->data["attributes"]);
                $this->data["files_uuid"] = $uuid;
                $this->data["conv_attr"] = $c;
                $this->data["total_attr"] = $t;
                $this->data["attributes"] = json_encode($attributes);
                $this->Create2();
            }
        }
        foreach($stats as $feature_name => $feature) {
            $this->insertInto = "f5_stats_features";
            $this->data = array(
                "files_uuid" => $uuid,
                "name"       => $feature_name,
                "modules"    => count($feature["modules"]),
                "objects"    => $feature["objects"],
                "attributes" => $feature["total"],
                "converted"  => $feature["converted"]
                );
            $this->Create2("id");
            $f_id = $this->id;
            foreach($feature["modules"] as $module_name => $module) {
                $this->insertInto = "f5_stats_modules";
                $this->data = array(
                    "files_uuid" => $uuid,
                    "feature_id" => $f_id,
                    "name"       => $module_name,
                    "objects"    => $module["objects"],
                    "attributes" => $module["total"],
                    "converted"  => $module["converted"]
                    );
                $this->Create2();
            }
        }
    }
    
}

/**
 * 
 */
class Condition {
    public $oper;
    public $A;
    public $B;

    function __construct($A, $o, $B) {
        if(is_string($A) || is_numeric($A)) {
            $this->A = new Column($A);
        } else {
            $this->A = $A;
        }
        $this->oper = $o;
        if(is_string($B) || is_numeric($B)) {
            $this->B = new Value($B);
        } else {
            $this->B = $B;
        }
    }
    function toString() {
        if(!isset($this->A) || !isset($this->oper) || !isset($this->B)) return "";
        $outA = $this->A->toString(); 
        $outB = $this->B->toString();
        $outOper = $this->oper;
        return "($outA $outOper $outB)";
    }
    function toValue(&$arr) {
        if(!is_array($arr)) return;
        $this->A->toValue($arr);
        $this->B->toValue($arr);
    }
    function add($o, $B) {
        $this->A = new Condition($this->A, $this->oper, $this->B);
        $this->oper = $o;
        $this->B = $B;
    }
}

class Column {
    public $name;

    function __construct($v) {
        $this->name = $v;
    }
    function toString() {
        return $this->name;
    }
    function toValue(&$arr) {}
}

class Value {
    public $v;
    function __construct($v) {
        $this->v = $v;
    }
    function toString() {
        return "?";
    }
    function toValue(&$arr) {
        if(!is_array($arr)) return;
        array_push($arr, $this->v);
    }
}
