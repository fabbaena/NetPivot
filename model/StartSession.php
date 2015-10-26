<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StarSession
 *
 * @author gonzalorodriguez
 */

  class StartSession {
     function __construct() {
     session_start ();
  }
  public function set($nombre, $valor) {
     $_SESSION [$nombre] = $valor;
  }
  public function get($nombre) {
     if (isset ( $_SESSION [$nombre] )) {
        return $_SESSION [$nombre];
     } else {
         return false;
     }
  }
  
  public function elimina_variable($nombre) {
      unset ( $_SESSION [$nombre] );
  }
  public function termina_sesion() {
      $_SESSION = array();
      session_destroy ();
  }
}
?>
