<?php

require_once dirname(__FILE__) .'/engine/functions.php';
require_once dirname(__FILE__) .'/model/UserList.php';

$email = get_email($_GET);
$token = get_token($_GET);

$u = new User(array('email' => $email, 'validation_string' => $token));
if($u->valid_token()) $uid = $u->id;


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('engine/css.php');?>
        <title>NetPivot - Password Reset</title>  
        <script language="javascript" src="js/validator.js"></script>
	    <script language="javascript">
        function sendemail() {
            $.getJSON( "engine/reset_pass.php", {"email": $("#email").val()} , function( data ) {
                    $(".panel-body").html($("<p>").html(data.message));
                    $(".panel-body").append($("<a>").attr("href", "/").html("Login"));
                });
        }

        function resetpass() {
            $.getJSON( "engine/reset_pass.php", {
                    "email": "<?= isset($email)?$email:"" ?>",
                    "token": "<?= isset($token)?$token:"" ?>",
                    "password": $("#password").val()
                } , function( data ) {
                    $(".panel-body").html($("<p>").html(data.message));
                    $(".panel-body").append($("<a>").attr("href", "/").html("Login"));
                });
        }
	    $().ready( function() {
            $("#account_options").remove();
            $('#form').validator().on('submit', function (e) {
                if (e.isDefaultPrevented()) {
                    alert('There are errors in the form. Please review the data entered.');
                } else {
                    <?= isset($uid)?"resetpass":"sendemail" ?>();
                }
                return false;
            })
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
                <form id="form" data-toggle="validator" role="form">
                <?php if(!isset($uid)) { ?>
                <div class="form-group has-feedback">
                    <label class="control-label" for="email">E-Mail:</label>
                    <input class="form-control" id="email" type="text" name="email" placeholder="John.Doe@citrix.com" pattern="^[._A-z0-9]{1,}@[._A-z0-9]{1,}\.[_A-z]{1,}$" data-pattern-error="Please use a valid email. No spaces" required>
                    <div class="help-block with-errors"></div>
                </div>
                <?php } else { ?>
                <div class="form-group has-feedback">
                    <label class="control-label" for="password">Password:</label>
                    <input class="form-control" id="password" type="password" name="password" pattern="^[!-}]{8,20}$" data-pattern-error="Please use between 8 and 20 alphanumeric or any of the following characters !@#$%^&*()-_=+{}[]\|;:\',.<>/?' . No spaces" required>
                    <div class="help-block with-errors"></div>
                </div>
                <div class="form-group has-feedback">
                    <label class="control-label" for="reppass">Repeat Password:</label>
                    <input class="form-control" id="reppass" type="password" name="reppass" pattern="^[!-}]{8,20}$" data-pattern-error="Please use between 8 and 20 alphanumeric or any of the following characters !@#$%^&*()-_=+{}[]\|;:\',.<>/?' . No spaces" data-match="#password" data-match-error="Passwords don't match." required>
                    <div class="help-block with-errors"></div>
                </div>
                <?php } ?>
                <br>
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Reset Password</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </body>
</html>