<?php
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/Event.php';
require_once dirname(__FILE__) .'/../model/FileManager.php';
require_once dirname(__FILE__) .'/../model/Conversions.php';

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

$conversion = new Conversion(array('files_uuid' => $uuid));
$conversion->load('files_uuid');
$projectid = $conversion->projectid;


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <script src="/js/Chart.min.js"></script>
        <?php include ('../engine/css.php'); ?>
        <title>NetPivot</title>
        <script language="javascript">
        var conversionid = <?= $conversion->id ?>;
        <?php if(isset($conversion->projectid)) { ?>var projectid = <?= $conversion->projectid?>; <?php } ?>

        $().ready( function() {
            $("#home").click(function() {document.location="./";});
            $("#conversionmanager").click(function() {document.location="convert.php";});
            $("#nav_dashboard").click(showBrief);
            $("#nav_objects").click(showObjects);
            $("#nav_modules").click(showModules);
            $('[data-toggle="popover"]').popover();

            loaddata();
            })
        </script>
    </head>
    <body>
    
    <?php include ('../engine/menu1.php');?>
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-10 col-md-offset-1 content">
            <ol class="breadcrumb panel-heading"></ol>
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
                        <div id="action_buttons" class="col-md-5 text-right">
                            <a href="../engine/Source.php?uuid=<?=$uuid?>" class="btn btn-warning">Download F5</a>
                            <a href="../engine/Download.php?uuid=<?=$uuid?>" class="btn btn-success">Download NS</a>
                            <?php if(isset($conversion->projectid)) { ?>
                            <a href="../engine/Reports.php?uuid=<?= $uuid ?>&projectid=<?= $conversion->projectid ?>" target="_blank" class="btn btn-danger">Report</a><?php } ?>
                        </div>
                    </div>
                </div>
    	        <div class="panel-body" id="content"></div>
            </div>
        </div>
    </div>
    </div>
    <footer class="pull-left footer">
        <p class="col-md-12">
            <hr class="divider">
        </p>
    </footer>
    <div class="modal fade" id="associateProject" tabindex="-1" role="dialog" aria-labelledby="assocPModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="assocPModalLabel">Associate Project</h4>
                </div>
                <div class="modal-body">
                    <div id="customer">
                        Please choose the Customer associated with this conversion:
                        <div class="dropdown">
                            <button id="clistLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select a Customer<span class="caret"></span></button>
                            <ul id="projects" class="dropdown-menu" aria-labelledby="clistLabel">
                                <li><a href="#">hello1</a></li>
                                <li><a href="#">hello2</a></li>
                                <li><a href="#">hello3</a></li>
                                <li><a href="#">hello4</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="hidden" id="project">
                        Please choose the Quote associated with this conversion:
                        <div class="dropdown">
                            <button id="qlistLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select a Quote<span class="caret"></span></button>
                            <ul id="quotes" class="dropdown-menu" aria-labelledby="qlistLabel">
                                <li><a href="#">hello1</a></li>
                                <li><a href="#">hello2</a></li>
                                <li><a href="#">hello3</a></li>
                                <li><a href="#">hello4</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button id="modalSave" type="button" class="btn btn-disabled">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
