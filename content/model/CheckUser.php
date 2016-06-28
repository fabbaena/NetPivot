<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CheckUser
 *
 * @author gonzalorodriguez
 */

class CheckUser {
   
    public $name;
    public $password;
    public $mensaje;
    public $email;
    public $id;
    public $user_type;
    public $max_files;
    public $roles;
    public $starturl;

    public function login(){
        $model = new Crud();
        $model->select = '*';
        $model->from = 'users';
        $model->condition = "name='". $this->name."'";
        $model->Read();
        $total = $model->rows;
        if ($total) {
            foreach ($total as $check) {
                if (password_verify($this->password, $check['password'])){
                    $this->id = $check['id'];
                    $this->user_type = $check['type'];
                    $this->max_files = $check['max_files'];
                    $this->mensaje = true;
                } else {
                    $this->mensaje = false;
                }
            }                
        } else {
            $this->mensaje = false;
        }
    }

    public function getRoles() {
        $this->starturl = "";
        $model = new Crud();
        $model->select = '*';
        $model->from = 'user_role_view';
        $model->condition = "username='". $this->name."'";
        $model->Read();
        $roles = $model->rows;
        if($roles) {
            foreach ($roles as $key => $value) {
                $keyname = $value["roleid"];
                if($this->starturl == "") $this->starturl = $value["starturl"];
                $this->roles[$keyname]["username"] = $value["username"];
                $this->roles[$keyname]["userid"] = $value["userid"] + 0;
                $this->roles[$keyname]["rolename"] = $value["rolename"];
                $this->roles[$keyname]["roleid"] = $value["roleid"] + 0;
                $this->roles[$keyname]["starturl"] = $value["starturl"];
            }
        }
    }
}