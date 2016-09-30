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
        <title>NetPivot - Domain Administration</title>  
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
                <li><a id="domainmanagement" href="#">Domain Management</a></li>
                <li class="active">New Domain</li>
            </ol>
            <div class="panel-body">
                <form class="form-newuser" role="form" data-toggle="validator" method="POST" action="../engine/add_domain.php"> 
                    <div class="form-group has-feedback">
                        <label class="control-label" for="domainname">Domain Name:</label>
                        <input class="form-control" id="domainname" type="text" name="domainname" placeholder="citrix.com" pattern="^[._A-z0-9]{1,}\.[_A-z0-9]{1,}$" data-match-error="Please use a valid email. No spaces" required>
                    </div>
                    <br>
                    <div class="form-group">
	                    <button type="submit" class="btn btn-success">Create Domain</button>
	                </div>
                </form>
            </div>
        </div>
    </div>
    </body>
</html>