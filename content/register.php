<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('engine/css.php');?>
        <title>NetPivot - Registration</title>  
	    <script language="javascript">
        function register() {
            $.getJSON( "engine/register.php", {
                "email": $("#email").val(),
                "company": $("#company").val(),
                "position": $("#position").val(),
                "firstname": $("#firstname").val(),
                "lastname": $("#lastname").val()
            } , function( data ) {
                    $(".panel-body").html($("<p>").html(data.message));
                    $(".panel-body").append($("<a>").attr("href", "/").html("Login"));
                });
        }

	    $().ready( function() {
            $("#account_options").remove();
	        $(".register").click(register);
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
                <div class="form-group has-feedback">
                    <p>Welcome to NetPivot registration.</p><p> Fill all the fields in this form and you'll receive an email to assing a password to your account and complete the registration.</p>
                    <label class="control-label" for="firstname">First Name:</label>
                    <input class="form-control" id="firstname" type="text" name="firstname" placeholder="John" required>
                    <label class="control-label" for="lastname">Last Name:</label>
                    <input class="form-control" id="lastname" type="text" name="lastname" placeholder="Doe" required>
                    <label class="control-label" for="email">E-Mail:</label>
                    <input class="form-control" id="email" type="text" name="email" placeholder="John.Doe@citrix.com" pattern="^[._A-z0-9]{1,}@[._A-z0-9]{1,}\.[A-z]{1,}$" data-match-error="Please use a valid email. No spaces" required>
                    <label class="control-label" for="company">Company:</label>
                    <input class="form-control" id="company" type="text" name="company" placeholder="Citrix" required>
                    <label class="control-label" for="position">Position:</label>
                    <input class="form-control" id="position" type="text" name="position" placeholder="Sales Engineer" required>
                </div>
                <br>
                <div class="form-group">
                    <button class="btn btn-success register">Register</button>
                </div>
            </div>
        </div>
    </div>
    </body>
</html>