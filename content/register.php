<?php
$saml = false;
if(isset($_GET["NetPivotUID"]) && 
    isset($_GET["givenName"]) && 
    isset($_GET["sn"]) &&
    isset($_GET["company"])) {
    $NetPivotUID = $_GET['NetPivotUID'];
    $givenName = $_GET['givenName'];
    $sn = $_GET['sn'];
    $company = $_GET['company'];
    $saml = true;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('engine/css.php');?>
        <title>NetPivot - Registration</title>
        <script language="javascript" src="js/validator.js"></script>
	    <script language="javascript">
        function register() {
            $.getJSON( "engine/register.php", 
                {
                    "email": $("#email").val(),
                    "company": $("#company").val(),
                    "position": $("#position").val(),
                    "firstname": $("#firstname").val(),
                    "lastname": $("#lastname").val(),
                    "saml": <?= $saml ? "true" : "false" ?>
                }, 
                function( data ) {
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
                    register();
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
            <div class="panel-heading">
                <h4>Registration</h4>
            </div>
            <div class="panel-body">
                <form id="form" role="form" data-toggle="validator">
                <div class="form-group has-feedback">
                    <p>Welcome to NetPivot registration.</p><p> Fill all the fields in this form and you'll receive an email to assing a password to your account and complete the registration.</p>
                    <label class="control-label" for="firstname">First Name:</label>
                    <input class="form-control" id="firstname" type="text" name="firstname" placeholder="John" pattern="^[.A-z0-9 ']{1,30}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters." <?= isset($givenName)? "value=\"$givenName\"" : "" ?> required>
                    <div class="help-block with-errors"></div>
                </div>
                <div class="form-group has-feedback">
                    <label class="control-label" for="lastname">Last Name:</label>
                    <input class="form-control" id="lastname" type="text" name="lastname" placeholder="Doe" pattern="^[.A-z0-9 ']{1,30}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters." <?= isset($sn)? "value=\"$sn\"" : "" ?> required>
                    <div class="help-block with-errors"></div>
                </div>
                <div class="form-group has-feedback">
                    <label class="control-label" for="email">E-Mail:</label>
                    <input class="form-control" id="email" type="text" name="email" placeholder="John.Doe@citrix.com" pattern="^[._A-z0-9]{1,}@[._A-z0-9]{1,}\.[A-z]{1,}$" data-pattern-error="Please use a valid email. No spaces" <?= isset($NetPivotUID)? "value=\"$NetPivotUID\"" : "" ?> required>
                    <div class="help-block with-errors"></div>
                </div>
                <div class="form-group has-feedback">
                    <label class="control-label" for="validateemail">Validate E-Mail:</label>
                    <input class="form-control" id="validateemail" type="text" name="validateemail" placeholder="John.Doe@citrix.com" data-match="#email" pattern="^[._A-z0-9]{1,}@[._A-z0-9]{1,}\.[A-z]{1,}$" data-pattern-error="Please use a valid email. No spaces" data-match-error="Please check your email." <?= isset($NetPivotUID)? "value=\"$NetPivotUID\"" : "" ?> required>
                    <div class="help-block with-errors"></div>
                </div>
                <div class="form-group has-feedback">
                    <label class="control-label" for="company">Company:</label>
                    <input class="form-control" id="company" type="text" name="company" placeholder="Citrix" pattern="^[.A-z0-9 ']{1,30}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters." <?= isset($company)? "value=\"$company\"" : "" ?> required>
                    <div class="help-block with-errors"></div>
                </div>
                <div class="form-group has-feedback">
                    <label class="control-label" for="position">Job Title:</label>
                    <input class="form-control" id="position" type="text" name="position" placeholder="Sales Engineer" pattern="^[.A-z0-9 ']{1,30}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters." required>
                    <div class="help-block with-errors"></div>
                </div>
                <br>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </body>
</html>