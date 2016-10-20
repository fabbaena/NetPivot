<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/FileManager.php';

$session = new StartSession();
$user = $session->get('user');
if(!$user) {
    header('location: /'); 
    exit();
}

if (!isset($_GET['file'])) {
    header('location: ./'); 
    exit();
}

$uuid = htmlspecialchars($_GET['file']);

$file = new FileManager(array('uuid' => $uuid));
$file->load('uuid');

if(!$user->has_role("System Admin") && $file->users_id != $user->id) {
    header('location: ./');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <?php include ('../engine/css.php');?>
    <script language="javascript">
    $().ready( function() {
        $("#home").click(function() {document.location="./";});
        $("#conversionmanager").click(function() {document.location="convert.php";});
        });
    </script>

    <title>Rename File</title>  
</head>
<body>
    <?php include ('../engine/menu1.php');?>
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <ol class="breadcrumb panel-heading">
                <li><a id="home" href="#">Home</a></li>
                <li><a id="conversionmanager">Conversion Manager</a></li>
                <li class="active">Rename File</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Rename File</h4>
                </div>
                <div class="panel-body">
                    <form action="GET">
                        <div class="form-group">
                            <label for="newname">New File Name:</label>  
                            <input type="text" name="newname" id="newname" value="<?= $file->filename ?>" size="40" required/><br/><br/>
                            <input type="hidden" name="uuid" id="uuid" value="<?= $file->uuid ?>">
                            <input type="submit" class="btn btn-primary margin-set" title="Rename" formaction="../engine/rename.php">                
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <footer class="pull-left footer ">
    <p class="col-md-12">
        <hr class="divider">
    </p>
    </footer>

</body>
</html>
