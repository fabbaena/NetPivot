
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
$admin = $user->has_role("Company Admin");

if(isset($_GET['e'])) {
    $reterr = $_GET['e'];
} else {
    $reterr = 0;
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('../engine/css.php');?>
        <script language="javascript">
        $().ready(function() {
            $("#conv_tool").click(function() {document.location="convert.php";});
            $("#hw_tool").click(function() {document.location="compare.php";});
            $("#event_log").click(function() {document.location="event_log.php";});
            $("#usage_stats").click(function() {document.location="usage_stats.php";});
            $("#Customer").click(function() {document.location="Customer/";});
            $("#Project").click(function() {document.location="Project/";});
        });
        </script>
        <title>NetPivot</title>  
    </head>
    <body>
    <?php include ('../engine/menu1.php');?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-8 col-sm-offset-2">
                <div class="jumbotron text-center">
                    <h1 class="display-3">Welcome to NetPivot</h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-offset-1 col-md-5 content">
                <button id="conv_tool" type="button" class="btn btn-secondary btn-block btn-lg">
                    F5 Configuration Converter
                </button>
            </div>
            <div class="col-xs-12 col-md-5 content">
                <button id="hw_tool" type="button" class="btn btn-secondary btn-block btn-lg">
                    Hardware Compare
                </button>
            </div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-xs-12 col-md-offset-1 col-md-5 content">
                <button id="event_log" type="button" class="btn btn-secondary btn-block btn-lg">
                    Event Log
                </button>
            </div>
            <?php if($admin) { ?>
            <div class="col-xs-12 col-md-5 content">
                <button id="usage_stats" type="button" class="btn btn-secondary btn-block btn-lg">
                    Usage Statistics
                </button>
            </div>
            <?php } ?>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-xs-12 col-md-offset-1 col-md-5 content">
                <button id="Customer" type="button" class="btn btn-secondary btn-block btn-lg">
                    Customers
                </button>
            </div>
            <div class="col-xs-12 col-md-5 content">
                <button id="Project" type="button" class="btn btn-secondary btn-block btn-lg">
                    Quotes
                </button>
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

