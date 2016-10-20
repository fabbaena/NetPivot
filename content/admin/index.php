
    <?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
 
$session = new StartSession();
$user = $session->get('user');
$session->set('filename', '');

if(!($user && $user->has_role("System Admin"))) { 
    header('location: /'); 
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('../engine/css.php'); ?>
        <title>NetPivot</title>  
    </head>
    <body>
    
    <?php include ('../engine/menu1.php'); ?>
	
    <div class="container-fluid">
    <div class="col-md-1"></div>
    <div class="col-md-10 content">
        <div class="panel panel-default">
            <ol class="breadcrumb panel-heading">
                <li>Admin Console</li>
            </ol>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <a href="admin_users.php">User Administration</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <a href="admin_domains.php">Domain Administration</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-1"></div>
    <footer class="pull-left footer">
        <p class="col-md-12">
            <hr class="divider">
        </p>
     </footer>
</body>
</html>
