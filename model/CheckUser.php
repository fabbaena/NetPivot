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
    
    public function login(){
        $model = new Crud();
        $model->select = '*';
        $model->from = 'users';
        $model->condition = 'name="'. $this->name.'"';
        $model->Read();
        $total = $model->rows;
        if ($total) {
            echo 'entro al total';
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
}