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
  private $path_files;

  function __construct($path_files) {
    $this->path_files = $path_files;
  }
  
  public function ListFiles() {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->path_files));
    $it->rewind();
    $this -> array_files = $it;
  }
    
  public function DeleteFile() {
    $delete = $this->file;
    try {
        unlink($this->path_files . $delete);
        $this-> message = 'true';
      } catch (Exception $ex) {
        $error = $e->getMessage();
        $this-> message = $error;
    }

  }

  public function CheckFile() {
    $filecheck = $this->file;
    $directory = $this->path_files;
    $path = $directory.$filecheck;
    if (file_exists($path)) {
      $this->message = true;
    } else {
      $this->message = false;
    }
  }
    
  public function GetNumbers($name){
    $file = file_get_contents($name);
    preg_match_all('!\d+!', $file, $matches);
    return $matches;
  }
}