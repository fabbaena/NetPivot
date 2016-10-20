<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
 
$session   = new StartSession();
$user   = $session->get('user');

if(!($user && $user->has_role("System Admin"))) {
    header('location: ../');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('../engine/css.php'); ?>
        <title>NetPivot - User Administration</title>  
        <script language="javascript" src="../js/validator.js"></script>
	    <script language="javascript">
        function save() {
            $.ajax( {
                type: "POST",
                url: "engine/register.php",
                data: {
                    "username": $("#username").val(),
                    "password": $("#password").val(),
                    "max_files": $("#max_files").val(),
                    "max_conversions": $("#max_conversions").val(),
                    "email": $("#email").val(),
                    "company": $("#company").val(),
                    "position": $("#position").val(),
                    "firstname": $("#firstname").val(),
                    "lastname": $("#lastname").val()
                    },
                success: function( data ) {
                    $(".panel-body").html($("<p>").html(data.message));
                    $(".panel-body").append($("<a>").attr("href", "/").html("Login"));
                    },
                dataType: 'json'
            });
        }

	    $().ready( function() {
            $("#usermanagement").click(function() {document.location="admin_users.php";});
            $("#adminconsole").click(function() {document.location="./";});
            loadRoles();
            $('#form').validator().on('submit', function (e) {
                if (e.isDefaultPrevented()) {
                } else {
                    save();
                }
                return false;
            })
		    })
	    </script>
    </head>
    <body >
    <?php include ('../engine/menu1.php');//Include the first part of the nav bar ?>
    <div class="col-md-1"></div>
    <div class="col-md-10 content">
        <div class="panel panel-default">
            <ol class="breadcrumb panel-heading">
                <li><a id="adminconsole" href="#">Admin Console</a></li>
                <li><a id="usermanagement" href="#">User Management</a></li>
                <li class="active">New User</li>
            </ol>
            <div class="panel-body">
                <form class="form-newuser" role="form" data-toggle="validator" method="POST" action="../engine/add_user.php"> 
                    <div class="row">
                        <div class="col-sm-6 form-group has-feedback">
                            <label class="control-label" for="firstname">First Name:</label>
                            <input class="form-control" id="firstname" type="text" name="firstname" placeholder="John" pattern="^[.A-z0-9 \']{1,30}$" data-pattern-error="Please use just letters and numbers. No spaces" required>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-sm-6 form-group has-feedback">
                            <label class="control-label" for="lastname">Last Name:</label>
                            <input class="form-control" id="lastname" type="text" name="lastname" placeholder="Doe" pattern="^[.A-z0-9 \']{1,30}$" data-pattern-error="Please use just letters and numbers. No spaces" required>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group has-feedback">
                            <label class="control-label" for="company">Company:</label>
                            <input class="form-control" id="company" type="text" name="company" placeholder="Citrix" pattern="^[.A-z0-9 \']{1,30}$" data-pattern-error="Please use just letters and numbers. No spaces" required>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-sm-6 form-group has-feedback">
                            <label class="control-label" for="position">Job Title:</label>
                            <input class="form-control" id="position" type="text" name="position" placeholder="Sales Engineer" pattern="^[.A-z0-9 \']{1,30}$" data-pattern-error="Please use just letters and numbers. No spaces" required>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label" for="email">E-Mail:</label>
                        <input class="form-control" id="email" type="text" name="email" placeholder="John.Doe@citrix.com" pattern="^[._A-z0-9]{1,}@[._A-z0-9]{1,}\.[_A-z]{1,}$" data-pattern-error="Please use a valid email. No spaces" required>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label" for="username">Username:</label>
                        <input class="form-control" id="username" type="text" name="username" placeholder="JohnDoe" pattern="^[_A-z0-9@.]{1,}$" data-pattern-error="Please use just letters and numbers. No spaces" required>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="control-label">Password:</label>
                        <div class="row">
	                        <div class="form-group col-sm-6">
	                            <input class="form-control" data-minlength="6" type="password" id="pwd" name="password" placeholder="Password" pattern="^[!-}]{8,20}$" data-pattern-error="Please use between 8 and 20 alphanumeric or any of the following characters !@#$%^&*()-_=+{}[]\|;:\',.<>/?' . No spaces" required>
	                            <div class="help-block">Between 8 and 20 characters</div>
	                        </div>
	                        <div class="form-group col-sm-6">
	                            <input class="form-control" type="password" id="pconfirm" data-match="#pwd" data-match-error="Whoops, these don't match" placeholder="Confirm" pattern="^[!-}]{8,20}$" data-pattern-error="Please use between 8 and 20 alphanumeric or any of the following characters !@#$%^&*()-_=+{}[]\|;:\',.<>/?' . No spaces" required>
	                            <div class="help-block with-errors"></div>
	                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group has-feedback">
                            <label for="files" class="control-label">Max Number of files:</label>
                            <input class="form-control" type="number" name="max_files" value="1" required>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-sm-6 form-group has-feedback">
                            <label for="conversions" class="control-label">Max Number of conversions:</label>
                            <input class="form-control" type="number" name="max_conversions" value="1" required>
                            <div class="help-block with-errors"></div>
                        </div>
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