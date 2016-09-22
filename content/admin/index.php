
    <?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/StartSession.php';
require '../model/Crud.php';
require '../model/TimeManager.php';
 
$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
$id= $sesion->get('id'); //Get user id
$user_type = $sesion->get('type'); //Get user type = administrator or user
$max_files = $sesion->get('max_files');
$sesion->set('filename', '');
$roles = $sesion->get('roles');

if($usuario == false || !isset($roles[1])) { 
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
                <div class="row">
                    <div class="col-md-12">
                        <a href="settings.php">Time Zone Settings</a>
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
