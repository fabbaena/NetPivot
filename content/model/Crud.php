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
    
    
    public function Create(){
        $model = new ConnectionBD();
        $conexion = $model->conectar();
        $insertInto = $this->insertInto;
        $insertColumns = $this->insertColumns;
        $insertValues = $this->insertValues;
        $sql = "INSERT INTO $insertInto ($insertColumns) VALUES ($insertValues)";
        $consulta = $conexion->prepare($sql);
        if (!$consulta){
            $this->mensaje = errorInfo();
        } else {
            $consulta->execute();
            $this->mensaje = TRUE;
            $this->id = $conexion->lastInsertId();
        }  
    }
    
    public function Read(){
        $model = new ConnectionBD();
        $conexion = $model->conectar();
        $select = $this->select;
        $from = $this->from;
        $condition = $this->condition;
        if ( $condition != ''){
            $condition = " WHERE " .$condition;
        }
        $sql = "SELECT $select FROM $from $condition";
        $consulta = $conexion->prepare($sql);
        $consulta->execute();
        while ($filas = $consulta->fetch()){
            $this->rows[] = $filas;
        }

    }
    
    
    public function Update(){
        $model = new ConnectionBD();
        $conexion = $model->conectar();
        $update = $this->update;
        $set = $this->set;
        $condition = $this->condition;
        if ($condition != ''){
            $condition = 'WHERE ' . $condition;
        }
        $sql = "UPDATE $update set $set $condition";
        $consulta = $conexion->prepare($sql);
        if (!$consulta){
            $this->mensaje = errorInfo();
        } else {
            $consulta->execute();
            $this->mensaje = TRUE;
        }
    }
    
    public function Delete(){
        $model = new ConnectionBD();
        $conexion = $model->conectar();
        $deleteFrom = $this->deleteFrom;
        $condition = $this->condition;
        if($condition != ''){
            $condition = ' WHERE ' . $condition;
        }
        $sql = "DELETE FROM $deleteFrom $condition";
        $consulta = $conexion->prepare($sql);
        if (!$consulta) {
            $this->mensaje = errorInfo();
        } else {
            $consulta->execute();
            $this->mensaje = TRUE;
        }
    }

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
