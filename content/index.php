<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require 'model/StartSession.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
$id= $sesion->get('id'); //Get user id
$user_type = $sesion->get('type'); //Get user type = administrator or user
$max_files = $sesion->get('max_files');
$roles = $sesion->get('roles');
$starturl = $sesion->get('starturl');

if($usuario == true && $roles == true) {
    $sesion->termina_sesion();  
    header('location: '. $starturl);
    exit();
}
?> 


<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('engine/css.php');?>
        <title>NetPivot</title>
        <script language="javascript">
        $().ready( function() {
            $(".resetpass").click(function() {document.location="reset_pass.php";});
            $(".register").click(function() { document.location="register.php";});
            })
        </script>
    </head>
    <body>
        <link href="css/signing.css" rel="stylesheet">
        <div class="container-fluid">  
            <form class="form-signin" method="POST" action="model/LoginUser.php">
                <div class="row">
                    <div class="col-md-3 col-md-offset-4">
                        <h2 class="form-signin-heading">Please Login</h2>
                            <label for="inputUsername">Username</label>
                            <input type="text" class="form-control" id="inputUsername" name="inputUsername" placeholder="Username"> 
                            <label for="inputPassword">Password</label>
                            <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Password">
                        <p class="text-danger"><?= isset($_GET['error'])? "Wrong username or password, please check.": "" ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 col-md-offset-4">
                    <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <a class="resetpass" href="#">Reset password</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <a class="register" href="#">Register</a>
                </div>
        </div> 
    </body>
</html>
