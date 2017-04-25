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

$filelist = new FileList(array('users_id' =>$id));

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
        <script src="/js/fileupload.js" language="javascript"></script>
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
                $.getJSON("../engine/GetCustomer.php", function(data) {
                    if(data['status'] == 'ok') {
                        $("#customers").html("");
                        for (var c in data["Customers"]) {
                            $("#customers").append($("<li>").html($("<a>")
                                .attr("customer_id", data["Customers"][c]["id"])
                                .attr("customer_name", data["Customers"][c]["name"])
                                .html(data["Customers"][c]["name"])
                                .click(clickCustomer)));
                        }
                        $("#fileUploadModal").modal('toggle');
                    }
                })
            });
            $.ajax( {
                url: "/engine/filelist.php",
                dataType: "json",
                success: showFilelist,
                error: function() { alert("error");}
                })
            });

            function clickCustomer(e) {
                var cid = $(e.target).closest("a").attr("customer_id");
                $("#clistLabel").html($(e.target).closest("a").attr("customer_name"));
                $("#qlistLabel").html("Select a Quote");
                $.getJSON("../engine/GetProject.php", { "customerid": cid }, function(data) {
                    if(data["status"] == "ok") {
                        $("#quotes").html("");
                        for (var q in data["projects"]) {
                            $("#quotes").append($("<li>").html($("<a>")
                                .attr("projectid", data["projects"][q]["id"])
                                .attr("project_name", data["projects"][q]["name"])
                                .html(data["projects"][q]["name"])
                                .click(clickProject)));
                        }
                        $("#project").removeClass("hidden");
                    }
                });
            }
            function clickProject(e) {
                var pid = $(e.target).closest("a").attr("projectid");
                $("#qlistLabel").html($(e.target).attr("project_name"));
                $("#fu").removeClass("hidden");
                $("#projectid").val(pid);
            }

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
        <div class="col-xs-12 col-md-10 col-md-offset-1 content">
            <?php include "file_list.inc" ?>
        </div>
    </div>
    <?php include "file_upload.inc"; ?>
    <?php include "reprocess.inc"; ?>
    <footer class="pull-left footer">
        <p class="col-md-12">
            <hr class="divider">
        </p>
    </footer>
</body>
</html>
