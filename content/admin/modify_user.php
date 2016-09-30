<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../engine/functions.php';
 
$session   = new StartSession();
$user   = $session->get('user');

if(!($user && $user->has_role("System Admin"))) {
    header('location: ../');
    exit();
}
$user_id = get_int($_GET, 'id');
?>
<!DOCTYPE html>
<html lang="en">
    <head>

        <?php include ('../engine/css.php');?>
        <title>NetPivot</title>
        <script language="javascript" src="../js/validator.js"></script>
        <script language="javascript">
        var modified = {};
        var userdata;
        function modifyData(event) {
            if(event.target.type == "checkbox") {
                if(typeof modified.roles == "undefined") {
                    modified.roles = {};
                }
                modified.roles[event.target.id.substring(5)] = event.target.checked;
            } else {
                userdata[event.target.id] = event.target.value;
                modified[event.target.id] = event.target.value;
            }
        }
        function validate(event) {
            var test =1;
            if(typeof modified.password != "undefined") {
                if(modified.password != $("#pwdconfirm").val()) {
                    $("#password").trigger("focus");
                    alert("Passwords don't match");
                    event.preventDefault();
                }
            }
            $.getJSON("../engine/modify_user.php", modified, function(data) {
                    if(data == "OK") {
                        document.location = "admin_users.php";
                    }
                });
            event.preventDefault();
        }
        $().ready( function() {
            $("#usermanagement").click(function() {document.location="admin_users.php";});
            $("#adminconsole").click(function() {document.location="./";});
            modified.id = <?=$user_id?>;
            $("#password").change(modifyData);
            $(".form-submit").submit(validate);
            loadRoles(modifyData);
            loadUser(modified.id);

        });
        </script>
    </head>
    <body >
    <?php include ('../engine/menu1.php'); ?>
    <div class="col-md-1"></div>
    <div class="col-md-10 content">
        <div class="panel panel-default">
            <ol class="breadcrumb panel-heading">
                <li><a id="adminconsole" href="#">Admin Console</a></li>
                <li><a id="usermanagement" href="#">User Management</a></li>
                <li class="active">Modify User</li>
            </ol>
            <div class="panel-body">
                <h4>Settings</h4><hr >
                <form class="form-submit" role="form" data-toggle="validator" method="POST">
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
                        <label class="control-label" for="name">Username:</label>
                        <input class="form-control" id="name" type="text" name="username" placeholder="JohnDoe" pattern="^[_A-z0-9@.]{1,}$" data-pattern-error="Please use just letters and numbers. No spaces" required>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="control-label">Password:</label>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <input class="form-control" type="password" id="password" placeholder="Password">
                                <div class="help-block">Minimum of 6 characters</div>
                            </div>
                            <div class="form-group col-sm-6">
                                <input class="form-control" type="password" id="pwdconfirm" placeholder="Confirm">
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group has-feedback">
                            <label for="files" class="control-label">Max Number of files:</label>
                            <input class="form-control" type="number" id="max_files" name="max_files" value="1" required>
                            <div class="help-block with-errors"></div>
                        </div>
                        <div class="col-sm-6 form-group has-feedback">
                            <label for="conversions" class="control-label">Max Number of conversions:</label>
                            <input class="form-control" type="number" id="max_conversions" name="max_conversions" value="1" required>
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">User Roles</label>
                        <div class="btn-group" data-toggle="buttons"></div>
                    </div>
                    <br>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Save User</button>
                    </div>
                </form>
                <hr>
            </div>
        </div>    
    </div>
    <footer class="pull-left footer">
        <p class="col-md-12">
            <hr class="divider">
        </p>
    </footer>
</body>
</html>
