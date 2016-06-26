<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/StartSession.php';
 
$sesion = new StartSession();
$usuario = $sesion->get('usuario');
$id= $sesion->get('id'); 
$user_type = $sesion->get('type');
$roles = $sesion->get('roles');


if($usuario == false || !isset($roles[1])) {
    header('location: ../');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('../engine/css.php');//Include links to stylesheets and js scripts ?>
        <title>NetPivot - User Administration</title>  
	    <script language="javascript">
	    $().ready( function() {
	        $(".btn-cancel").click(function() {document.location="admin_users.php";});
            loadRoles();
		    })
	    </script>
    </head>
    <body >
    <?php include ('../engine/menu1.php');//Include the first part of the nav bar ?>
    <div class="col-md-1"></div>
    <div class="col-md-10 content">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-4"><h4>New User</h4></div>
                    <div class="col-sm-8 text-right">
                        <div class="btn btn-default btn-cancel">Cancel</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <form class="form-newuser" role="form" data-toggle="validator" method="POST" action="../engine/add_user.php"> 
                    <div class="form-group has-feedback">
                        <label class="control-label" for="username">Username:</label>
                        <input class="form-control" id="username" type="text" name="username" placeholder="JohnDoe" pattern="^[_A-z0-9]{1,}$" data-match-error="Please use just letters and numbers. No spaces" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class="control-label">Password:</label>
                        <div class="row">
	                        <div class="form-group col-sm-6">
	                            <input class="form-control" data-minlength="6" type="password" id="pwd" name="password" placeholder="Password" required>
	                            <div class="help-block">Minimum of 6 characters</div>
	                        </div>
	                        <div class="form-group col-sm-6">
	                            <input class="form-control" type="password" id="pconfirm" data-match="#pwd" data-match-error="Whoops, these don't match" placeholder="Confirm" required>
	                            <div class="help-block with-errors"></div>
	                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="files" class="control-label">Max Number of files:</label>
                        <input class="form-control" type="number" name="max_files" value="1" size="30" required>
                    </div>
                    <div class="form-group">
                        <label for="conversions" class="control-label">Max Number of conversions:</label>
                        <input class="form-control" type="number" name="max_conversions" value="1" size="40" required>
                    </div>
                    <div class="form-group">
                    	<label class="control-label">User Roles</label>
                    	<div class="btn-group" data-toggle="buttons"></div>
                    </div>
                    <br>
                    <div class="form-group">
	                    <button type="submit" class="btn btn-success">Create User</button>
	                </div>
                </form>
            </div>
        </div>
    </div>
    </body>
</html>