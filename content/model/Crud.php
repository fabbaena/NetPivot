<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Crud
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
    public $data;

    private $_conn;
    

    function __construct() {
        $usuario = "demonio";
        $pass = "s3cur3s0c";
        $host = "localhost";
        $db = "netpivot";
        $this->_conn = new PDO("pgsql:host=$host;dbname=$db", $usuario, $pass);
    }
    
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

    public function Create2() {
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
        $consulta = $this->_conn->prepare($sql);
        if (!$consulta){
            $this->mensaje = errorInfo();
        } else {
            $consulta->execute($insertValuesArray);
            $this->mensaje = TRUE;
            $this->id = $this->_conn->lastInsertId();
        }  
    }
    
    public function Read(){
        $select = $this->select;
        $from = $this->from;
        $condition = $this->condition;
        if ( $condition != ''){
            $condition = " WHERE " .$condition;
        }
        $sql = "SELECT $select FROM $from $condition";
        $consulta = $this->_conn->prepare($sql);
        $consulta->execute();
        while ($filas = $consulta->fetch()){
            $this->rows[] = $filas;
        }

    }

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

    public function Load(){
        $filename = $this->filename;
        $uuid = $this->uuid;

        $consulta = $this->_conn->prepare('BEGIN');
        $consulta->execute();
        $sql = "INSERT INTO details (module, obj_grp, obj_component, obj_name, attribute, converted, omitted, line, files_uuid) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $consulta = $this->_conn->prepare($sql);
        $csvfile = fopen($filename, "r") or die("Unable to open $filename!");
        while($line = fgets($csvfile)) {
            $data = explode(",", $line);
            $arr = array_splice($data, 0, 8);
            array_push($arr, $uuid);
            $consulta->execute($arr);
        }
        fclose($csvfile);
        $consulta = $this->_conn->prepare('COMMIT');
        $consulta->execute();
        $this->mensage = $consulta->errorInfo();
    }

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
    
}
