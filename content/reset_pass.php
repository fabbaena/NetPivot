<?php


require 'model/Crud.php';

if(isset($_GET['token']) && isset($_GET['email'])) {
    $email = urldecode($_GET['email']);
    $token = urldecode($_GET['token']);

    $model = new Crud();
    $model->select = "id";
    $model->from = "users";
    $model->condition = "email='$email' AND validation_string='$token'";
    $model->Read();
    if(isset($model->rows)) {
        $uid = $model->rows[0]['id'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('engine/css.php');?>
        <title>NetPivot - Password Reset</title>  
	    <script language="javascript">
        function sendemail() {
            $.getJSON( "engine/reset_pass.php", {"email": $("#email").val()} , function( data ) {
                    $(".panel-body").html($("<p>").html(data.message));
                    $(".panel-body").append($("<a>").attr("href", "/").html("Login"));
                });
        }

        function resetpass() {
            if($("#password").val() != $("#reppass").val()) {
                alert("Passwords don't match. Try Again.");
                return;
            }
            $.getJSON( "engine/reset_pass.php", {
                    "email": "<?= isset($email)?$email:"" ?>",
                    "token": "<?= isset($token)?$token:"" ?>",
                    "newpass": $("#password").val()
                } , function( data ) {
                    $(".panel-body").html($("<p>").html(data.message));
                    $(".panel-body").append($("<a>").attr("href", "/").html("Login"));
                });
        }
	    $().ready( function() {
            $("#account_options").remove();
	        $(".resetpass").click(<?= isset($uid)?"resetpass":"sendemail" ?>);
		  })
	    </script>
    </head>
    <body >
    <?php include ('engine/menu1.php');//Include the first part of the nav bar ?>
    <div class="col-md-1"></div>
    <div class="col-md-10 content">
        <div class="panel panel-default">
            <ol class="breadcrumb panel-heading">
            </ol>
            <div class="panel-body">
                <div class="form-group has-feedback">
                <?php if(!isset($uid)) { ?>
                    <label class="control-label" for="email">E-Mail:</label>
                    <input class="form-control" id="email" type="text" name="email" placeholder="John.Doe@citrix.com" pattern="^[._A-z0-9]{1,}@[._A-z0-9]{1,}\.[_A-z]{1,}$" data-match-error="Please use a valid email. No spaces" required>
                <?php } else { ?>
                    <label class="control-label" for="password">Password:</label>
                    <input class="form-control" id="password" type="password" name="password" required>
                    <label class="control-label" for="reppass">Repeat Password:</label>
                    <input class="form-control" id="reppass" type="password" name="reppass" required>
                <?php } ?>
                </div>
                <br>
                <div class="form-group">
                    <button class="btn btn-success resetpass">Reset Password</button>
                </div>
            </div>
        </div>
    </div>
    </body>
</html>