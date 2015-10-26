<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileManager
 *
 * @author gonzalorodriguez
 */

class FileManager {
    
    public $file;
    public $array_files;
    public $message;
    
    public function ListFiles() {
       $directory = "files/";
       $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
       $it->rewind();
       $this -> array_files = $it;
    }
    
    public function DeleteFile() {
       $delete = $this -> file;
       try {
           unlink('../dashboard/files/' . $delete);
           $this-> message = 'true';
       } catch (Exception $ex) {
           $error = $e->getMessage();
           $this-> message = $error;
       }
       
    }
    
    public function CheckFile() {
       $filecheck = $this->file;
       $directory = "../dashboard/files/";
       $path = $directory.$filecheck;
        if (file_exists($path)) {
            $this->message = true;
        } else {
            $this->message = false;
        }
    }
}