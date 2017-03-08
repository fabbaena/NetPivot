
    <?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/FileManager.php';
require_once dirname(__FILE__) .'/../model/Event.php';

$session = new StartSession();
$user = $session->get('user');
$admin = $user->has_role("Company Admin");

if(!$user || !$admin) { 
    header('location: /'); 
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('../engine/css.php');?>
        <script language="javascript">

        function load_filter(days) {
            $.ajax( {
                url: "/engine/usage_stats.php",
                data: { 
                    "etype": event_type,
                    "days": last_x
                },
                dataType: "json",
                success: function(data) {
                    $("#usage_stats").html("");
                    $.each(data.stats, print_event);
                },
            });
        }

        var event_type = 1;
        var last_x = 2;

        $().ready(function() {
            $("#home").click(function() {document.location="./";});

            $("#filter").on("click", function(e, data) { load_filter(); });
            $("#lastx_items").on("click", select_last);
            $("#etype_items").on("click", select_etype);
            load_filter();

        });
        function select_last(e, data) {
            $("#lastx").html(e.target.innerHTML + " ");
            $("#lastx").append($("<span>").addClass("caret"));
            last_x = $(e.target).attr("sdays");
            load_filter();
        }
        function select_etype(e, data) {
            $("#etype").html(e.target.innerHTML + " ");
            $("#etype").append($("<span>").addClass("caret"));
            event_type = $(e.target).attr("event_type");
            load_filter();
        }
        function print_event(i, e) {
            $("#usage_stats")
                .append($("<tr>")
                    .append($("<td>").html(e.username))
                    .append($("<td>").html(e.company))
                    .append($("<td>").html(e.event_count))
                );
        }
        </script>
        <title>NetPivot</title>  
    </head>
    <body>
    <?php include ('../engine/menu1.php');?>


    <div class="container-fluid">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <ol class="breadcrumb panel-heading">
                <li><a id="home" href="#">Home</a></li>
                <li class="active">Usage Statistics</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1"></div>
        <?php include "usage_stats.inc"; ?>
    </div>
    <footer class="pull-left footer">
        <p class="col-md-12">
            <hr class="divider">
        </p>
    </footer>
</body>
</html>
