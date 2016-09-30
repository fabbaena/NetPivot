<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/functions.php';

$session   = new StartSession();
$user   = $session->get('user');

$username        = get_username($_POST);
$password        = get_password($_POST);
$type            = get_validstring($_POST, 'type');
$max_files       = get_int($_POST, 'max_files');
$max_conversions = get_int($_POST, 'max_conversions');
$email           = get_email($_POST);
$company         = get_validstring($_POST, 'company');
$position        = get_validstring($_POST, 'position');
$firstname       = get_validstring($_POST, 'firstname');
$lastname        = get_validstring($_POST, 'lastname');

if(!($user && $user->has_role("System Admin"))) {
    header('location: ../');
    exit();
}

$newuser = new User(array(
    "name" => $username, 
    "type" => "", 
    "max_files" => $max_files, 
    "max_conversions" => $max_conversions, 
    "used_files" => 0,
    "used_conversions" => 0,
    "email" => $email,
    "company" => $company,
    "position" => $position,
    "firstname" => $firstname,
    "lastname" => $lastname
    ));

$newuser->password = $password;

for($i=1; $i<4; $i++) {
    if(isset($_POST["role_". $i])) {
        $role = new Role(array("id" => $i));
        $newuser->addRole($role);
    }
}

$newuser->save();

header('location: ../admin/admin_users.php')

?>



