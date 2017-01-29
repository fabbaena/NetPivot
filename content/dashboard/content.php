<?php
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/Event.php';
require_once dirname(__FILE__) .'/../model/FileManager.php';

$session = new StartSession();
$user = $session->get('user');

if(!($user && $user->has_role("Engineer")) ) { 
    header('location: /'); 
    exit();
}

if (isset($_POST['uuid'])) {
     $uuid = htmlspecialchars($_POST['uuid']);
     $session->set('uuid', $uuid);
} elseif (isset($_GET['uuid'])) {
     $uuid = htmlspecialchars($_GET['uuid']);
     $session->set('uuid', $uuid);
} else {
    $uuid = $session->get('uuid');
}

$file = new FileManager(array('uuid' => $uuid));
$file->load('uuid');
$file_name = $file->filename;

new Event($user, "Viewing file \"$file_name\".", 3);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <script src="/js/Chart.min.js"></script>
        <?php include ('../engine/css.php'); ?>
        <title>NetPivot</title>
        <script language="javascript">
        $().ready( function() {
            $("#home").click(function() {document.location="./";});
            $("#conversionmanager").click(function() {document.location="convert.php";});
            $("#nav_dashboard").click(showBrief);
            $("#nav_objects").click(showObjects);
            $("#nav_modules").click(showModules);

            loaddata();
            })
        </script>
    </head>
    <body>
    
    <?php include ('../engine/menu1.php');?>
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10 content">
            <ol class="breadcrumb panel-heading">
            </ol>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                    <div class="col-md-7">
                    <ul class="nav nav-tabs">
                        <li role="presentation" id="nav_dashboard" class="active"><a href="#">Dashboard</a></li>
                        <li role="presentation" id="nav_modules"><a href="#">Module Details</a></li>
                        <li role="presentation" id="nav_objects"><a href="#">Objects</a></li>
                    </ul>
                    </div>
                    <div class="col-md-5 text-right">
                        <a href="../engine/Source.php?uuid=<?=$uuid?>" class="btn btn-warning">Download F5</a>
                        <a href="../engine/Download.php?uuid=<?=$uuid?>" class="btn btn-success">Download NS</a>
                    </div>
                    </div>
                </div>
    	        <div class="panel-body" id="content">
    	        </div>
            </div>
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
