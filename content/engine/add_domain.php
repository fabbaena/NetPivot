<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/StartSession.php';
require '../model/Crud.php';
require '../model/DomainList.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario');
$id= $sesion->get('id'); 
$user_type = $sesion->get('type');
$roles = $sesion->get('roles');


if($usuario == false || !isset($roles[1]) || !isset($_POST['domainname']) ) {
    header('location: ../');
    exit();
}

$domainname = htmlspecialchars($_POST['domainname']);

$domain = new Domain(array("name" => $domainname));

$domain->save();

header('location: ../admin/admin_domains.php')

?>



