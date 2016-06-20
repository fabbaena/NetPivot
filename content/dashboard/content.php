<?php
require '../model/StartSession.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario'); 
$uuid = $sesion->get('uuid');
$roles = $sesion->get('roles');
if($usuario == false ) { 
    header('location: /'); 
    exit();
}

if (isset($_POST['uuid'])) {
     $uuid = htmlspecialchars($_POST['uuid']);
     $sesion->set('uuid', $uuid);
} 

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('../engine/css.php'); ?>
        <title>NetPivot</title>
    </head>
    <body onLoad="loaddata()">
    
    <?php include ('../engine/menu1.php');?>
    <div class="row">
    <div class="col-md-1"></div>
    <div class="col-md-10 content">
        <div class="panel panel-default">
            <div class="panel-body">
				<ol class="breadcrumb">
				</ol>
				<hr>
            </div>
	        <div class="panel-body" id="content">
				<div class="row" style="margin-bottom: 20px">
					<div class="col-xs-4 content">
					    <h2 class="filename nav_title">Dashboard</h2>
					</div>
					<div class="col-xs-push-8 navbuttons text-right">
					    <div class="btn btn-primary nav_buttons nav_brief disabled" onClick=showBrief()>Summary</div>
					    <div class="btn btn-primary nav_buttons nav_modules" onClick="showModules()">Module Details</div>
					    <div class="btn btn-primary nav_buttons nav_objects" onClick="showObjects()">Objects</div>
					    <a href="../engine/Download.php?uuid=<?=$uuid?>" class="btn btn-success">Download Target</a>
					</div>
				</div>
	        </div>
        </div>
    </div>
    <div class="col-md-1"></div>
    </div>
    <footer class="pull-left footer">
        <p class="col-md-12">
            <hr class="divider">
        </p>
     </footer>
</body>
</html>
