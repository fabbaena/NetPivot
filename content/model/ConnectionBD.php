<?php
class ConnectionBD {
    
public function conectar() {
    $usuario = "demonio";
    $pass = "s3cur3s0c";
    $host = "localhost";
    $db = "NetPivot";
    return $conexion = new PDO("mysql:host=$host;dbname=$db", $usuario, $pass);
}

}