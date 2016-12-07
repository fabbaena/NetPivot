<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) .'/model/StartSession.php';

$session = new StartSession();
$user = $session->get('user');

if(isset($user)) {
    $session->termina_sesion();  
    header('location: /');
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
            $(".saml").click(function() { document.location="saml.php";});
            })
        </script>
    </head>
    <body>
        <link href="css/signing.css" rel="stylesheet">
        <div class="container-fluid">  
            <form class="form-signin" method="POST" action="engine/loginuser.php">
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-sm-offset-4">
                        <h2 class="form-signin-heading">Please Login</h2>
                            <label for="inputUsername">Username</label>
                            <input type="text" class="form-control" id="inputUsername" name="username" placeholder="Username"> 
                            <label for="inputPassword">Password</label>
                            <input type="password" class="form-control" id="inputPassword" name="password" placeholder="Password">
                        <p class="text-danger"><?= isset($_GET['error'])? "Wrong username or password, please check.": "" ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 col-sm-offset-4">
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-sm-4 col-sm-offset-4">
                    <a class="resetpass" href="#">Reset password</a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-sm-offset-4">
                    <a class="register" href="#">Register</a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4 col-sm-offset-4">
                    If you are a Citrix employee, click <a class="saml" href="#">here</a>
                </div>
            </div>
        </div> 
    </body>
</html>
