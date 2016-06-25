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
$user_id = htmlspecialchars($_GET['id']);

?>

<!DOCTYPE html>
<html lang="en">
    <head>

        <?php include ('../engine/css.php');?>
        <title>NetPivot</title>
        <script language="javascript">
        var modified = {};
        function modifyData(event) {
            if(event.target.type == "checkbox") {
                if(typeof modified.roles == "undefined") {
                    modified.roles = {};
                }
                modified.roles[event.target.id.substring(5)] = event.target.checked;
            } else {
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
            modified.id = <?=$user_id?>;
            $(".btn-cancel").click(function() {document.location="admin_users.php";});
            $("#password").change(modifyData);
            $("#max_files").change(modifyData);
            $("#max_conversions").change(modifyData);
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
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-4"><h4 id="panel-title">Modify User </h4></div>
                    <div class="col-sm-8 text-right">&nbsp;
                        <div class="btn btn-default btn-cancel">Cancel</div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <h4>Settings</h4><hr >
                <form class="form-submit" role="form" data-toggle="validator" method="POST">
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
                    <div class="form-group">
                        <label for="files" class="control-label">Max Number of files:</label>
                        <input class="form-control" type="number" id="max_files" required>
                    </div>
                    <div class="form-group">
                        <label for="conversions" class="control-label">Max Number of conversions:</label>
                        <input class="form-control" type="number" id="max_conversions" required>
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
