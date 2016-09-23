<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/StartSession.php';
require '../model/Crud.php';
require '../model/CheckUser.php';
require '../model/UserList.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario');
$id= $sesion->get('id'); 
$user_type = $sesion->get('type');
$roles = $sesion->get('roles');


if($usuario == false || !isset($roles[1]) || 
    !isset($_POST['username']) || !isset($_POST['password']) ||
        !isset($_POST['max_files']) || !isset($_POST['max_conversions'])) {
    header('location: ../');
    exit();
}

$username = htmlspecialchars($_POST['username']);
$password = htmlspecialchars($_POST['password']);
$type = htmlspecialchars(isset($_POST['usertype'])?$_POST['usertype']:"");
$max_files = htmlspecialchars($_POST['max_files']);
$max_conversions = htmlspecialchars($_POST['max_conversions']);

$user = new User(array(
    "id" => 0, 
    "name" => $username, 
    "type" => "", 
    "max_files" => $max_files, 
    "max_conversions" => $max_conversions, 
    "used_files" => 0,
    "used_conversions" => 0
    ));

$user->password = $password;

for($i=1; $i<4; $i++) {
    if(isset($_POST["role_". $i])) {
        array_push($user->roles, $i);
    }
}

$user->save();

header('location: ../admin/admin_users.php')

?>



