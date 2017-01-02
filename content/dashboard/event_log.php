
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

if(!($user)) { 
    header('location: /'); 
    exit();
}

$admin = $user->has_role("Company Admin");
$username = $user->name;

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('../engine/css.php');?>
        <link rel="stylesheet" href="../css/jquery-ui.min.css">
        <script src="../js/jquery-ui.min.js"></script>
        <script language="javascript">

        function load_filter() {
            $.ajax( {
                url: "/engine/eventlist.php",
                data: { 
                    "oldest_timestamp": $( "#oldest_timestamp_picker" ).val(), 
                    "newest_timestamp": $( "#newest_timestamp_picker" ).val(),
                    "user_id": $("#user_id").val()
                },
                dataType: "json",
                success: function(data) {
                    $("#event_logs").html("");
                    $.each(data.events, print_event);
                },
            });
        }

        $().ready(function() {
            $( "#oldest_timestamp_picker" ).datepicker();
            $( "#oldest_timestamp_picker" ).datepicker("option", "dateFormat", "yy-mm-dd");
            $( "#newest_timestamp_picker" ).datepicker();
            $( "#newest_timestamp_picker" ).datepicker("option", "dateFormat", "yy-mm-dd");
            $("#home").click(function() {document.location="./";});

            var d = new Date()
            var month = d.getMonth() + 1;
            $( "#oldest_timestamp_picker" ).val(d.getFullYear()+"-"+month+"-"+d.getDate());
            $( "#newest_timestamp_picker" ).val(d.getFullYear()+"-"+month+"-"+d.getDate());
            $("#filter").on("click", function(e, data) {
                load_filter();
            });
            load_filter();

            $( "#user_name_input" ).autocomplete({
                source: "../engine/eventuserlist.php",
                minLength: 1,
                select: function( event, ui ) {
                    $("#user_id").val(ui.item.id);
                }
            });
        });
        function print_event(i, e) {
            var a = i;
            $("#event_logs")
                .append($("<tr>")
                    .append($("<td>").html(e.timestamp))
                    .append($("<td>").html(e.user_fullname))
                    .append($("<td>").html(e.company_name))
                    .append($("<td>").html(e.event))
                    )
                ;
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
                <li class="active">Event Log</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1"></div>
        <?php include "event_log.inc"; ?>
    </div>
    <footer class="pull-left footer">
        <p class="col-md-12">
            <hr class="divider">
        </p>
    </footer>
</body>
</html>
