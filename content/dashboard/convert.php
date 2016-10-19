
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

if(!($user && $user->has_role("Engineer") )) { 
    header('location: /'); 
    exit();
}
$id = $user->id;

$filelist = new FileList(array('users_id' =>$id));

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('../engine/css.php');?>
        <title>NetPivot</title>  
        <link rel="stylesheet" href="/css/jquery.fileupload.css">
        <script src="/js/vendor/jquery.ui.widget.js"></script>
        <script src="/js/jquery.fileupload.js"></script>
        <script src="/js/fileupload.js" language="javascript"></script>
        <script language="javascript">
        $().ready( function() {
            $("#home").click(function() {document.location="./";});
            })
        </script>
    </head>
    <body>
    <?php include ('../engine/menu1.php');?>
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <ol class="breadcrumb panel-heading">
                <li><a id="home" href="#">Home</a></li>
                <li class="active">Conversion Manager</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1"></div>
            <div class="col-xs-12 col-md-4 content">
                <?php include "file_upload.inc"; ?>
            </div>
            <div class="col-xs-12 col-md-6 content">
                <?php include "file_list.inc" ?>
            </div>
    </div>
    <footer class="pull-left footer">
        <p class="col-md-12">
            <hr class="divider">
        </p>
    </footer>
</body>
</html>
