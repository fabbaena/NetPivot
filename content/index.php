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

if($usuario == true ) {
    header('location:dashboard/index.php');
} else {
    ?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <!--   <meta name="viewport" content="width=device-width, initial-scale=1"> Bootstrap for mobile devices -->
        <title>NetPivot</title>
        <meta name="description" content="">
        <meta name="author" content="netpivot">
        <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
        <link rel="shortcut icon" href="/favicon.ico">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <meta name="viewport" content="width=device-width, initial-scale=1">
            <!-- CSS de Bootstrap -->
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
        <script src="ccs/jquery.js"></script>
    </head>
    <body>
        <script src="http://code.jquery.com/jquery.js"></script>
        <link href="css/signing.css" rel="stylesheet">
        <div class="container-fluid">  
            <form class="form-signin" method="POST" action="model/LoginUser.php">
                <h2 class="form-signin-heading"> Please Login</h2>
                    <label for="inputUsername">Username</label>
                    <input type="text" class="form-control" id="inputUsername" name="inputUsername" placeholder="Username"> 
                    <label for="inputPassword">Password</label>
                    <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Password">
                
                <button type="submit" class="btn btn-primary">Login</button>
                <?php 
                    if (isset($_GET['error'])) { 
                        echo '<p class="text-danger">Wrong username or password, please check.</p>';
                    }
                ?>
            </form>
        </div> 
    </body>
</html>
       <?php
}
?>         