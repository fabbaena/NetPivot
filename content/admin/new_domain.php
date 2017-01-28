<?php

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
 
$session = new StartSession();
$user = $session->get('user');
$session->set('filename', '');

if(!($user && $user->has_role("System Admin"))) { 
    header('location: /'); 
    exit();
}


$id= $user->id; 


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('../engine/css.php');//Include links to stylesheets and js scripts ?>
        <title>NetPivot - Company Administration</title>  
        <script language="javascript" src="/js/validator.js"></script>
	    <script language="javascript">
	    $().ready( function() {
	        $("#domainmanagement").click(function() {document.location="admin_domains.php";});
            $("#adminconsole").click(function() {document.location="./";});
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
                <li><a id="domainmanagement" href="#">Company Management</a></li>
                <li class="active">New Company</li>
            </ol>
            <div class="panel-body">
                <form class="form-newuser" role="form" data-toggle="validator" method="POST" action="../engine/add_domain.php"> 
                    <div class="form-group has-feedback">
                        <label class="control-label" for="name">Company Name:</label>
                        <input class="form-control" id="name" type="text" name="name" pattern="^[._A-z0-9 \-',]{3,}$" data-pattern-error="Invalid character. Use letters, numbers and the following characters (._-',)" required>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label" for="domain">Domain Name:</label>
                        <input class="form-control" id="domain" type="text" name="domain" pattern="^[._A-z0-9]{1,}\.[_A-z0-9]{1,}$" data-pattern-error="Please use a valid domain. No spaces" required>
                        <div class="help-block with-errors"></div>
                    </div>
                    <br>
                    <div class="form-group">
	                    <button type="submit" class="btn btn-success">Save</button>
	                </div>
                </form>
            </div>
        </div>
    </div>
    </body>
</html>