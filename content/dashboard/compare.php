
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
        <script src="/js/hw_compare.js" language="javascript"></script>        
        <script language="javascript">
        $().ready(function() {
            $("#home").click(function() {document.location="./";});
            hw_table();
        });
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
                <li class="active">Hardware Compare</li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1"></div>
        <?php include "hw_compare.inc"; ?>
    </div>
    <footer class="pull-left footer">
        <p class="col-md-12">
            <hr class="divider">
        </p>
    </footer>
</body>
</html>
