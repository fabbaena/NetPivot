<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/FileManager.php';
require_once dirname(__FILE__) .'/../engine/Config.php';

$session = new StartSession();
$user = $session->get('user');

if(!($user && $user->has_role("Engineer") )) { 
    header('location: /'); 
    exit();
}
$id = $user->id;

$c = new Config();

$v = substr(exec($c->np_version(), $v_out,$v_error), 17);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('../engine/css.php');?>
        <title>NetPivot</title>  
        <link rel="stylesheet" href="/css/jquery.fileupload.css">
        <script src="/js/vendor/jquery.ui.widget.js"></script>
        <script src="/js/jquery.fileupload.js"></script>
        <script language="javascript" src="/js/validator.js"></script>
        <script src="/js/nginx_fileupload.js" language="javascript"></script>
        <script language="javascript">
        var reprocess_uuid;
        var np_version = "<?= $v ?>";
        $().ready( function() {
            $("#home").click(function() {document.location="./";});
            initFileUpload();
            $("#start_rep").click(function(e, data) {
                purge();
                });
            $("#close_rep").click(function(e, data) {
                $("#lr").html("");
                });
            $("#btn_fileupload").click(function(e) {
                $("#fileUploadModal").modal('toggle');
            });
        });

        </script>
    </head>
    <body>
    <?php include ('../engine/menu1.php');?>
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <ol class="breadcrumb panel-heading">
                <li><a id="home" href="#">Home</a></li>
                <li class="active">Nginx Conversion Manager</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-md-10 col-md-offset-1 content">
            <?php include "nginx_file_list.inc" ?>
        </div>
    </div>
    <?php include "nginx_file_upload.inc"; ?>
    <footer class="pull-left footer">
        <p class="col-md-12">
            <hr class="divider">
        </p>
    </footer>
</body>
</html>
