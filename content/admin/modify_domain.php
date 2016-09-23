<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../model/StartSession.php';
require '../model/Crud.php';
require '../model/DomainList.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario');
$id= $sesion->get('id'); 
$user_type = $sesion->get('type');
$roles = $sesion->get('roles');


if($usuario == false || !isset($roles[1])) {
    header('location: ../');
    exit();
}
$domain_id = htmlspecialchars($_GET['id']);

$d = new Domain(array('id' => $domain_id));
$d->load();

?>

<!DOCTYPE html>
<html lang="en">
    <head>

        <?php include ('../engine/css.php');?>
        <title>NetPivot</title>
        <script language="javascript">
        $().ready( function() {
            $(".domainmanagement").click(function() {document.location="admin_domains.php";});
            $(".adminconsole").click(function() {document.location="./";});
        });
        </script>
    </head>
    <body >
    <?php include ('../engine/menu1.php'); ?>
    <div class="col-md-1"></div>
    <div class="col-md-10 content">
        <div class="panel panel-default">
            <ol class="breadcrumb panel-heading">
                <li><a class="adminconsole" href="#">Admin Console</a></li>
                <li><a class="domainmanagement" href="#">Domain Management</a></li>
                <li class="active">Modify Domain</li>
            </ol>
            <div class="panel-body">
                <h4>Settings</h4><hr >
                <form class="form-submit" role="form" data-toggle="validator" method="POST" action="../engine/modify_domain.php">
                    <div class="form-group">
                        <label class="control-label" for="domainname">Domain Name:</label>
                        <input class="form-control" id="domainname" type="text" name="name" pattern="^[._A-z0-9]{1,}\.[_A-z0-9]{1,}$" data-match-error="Please use a valid email. No spaces" value="<?= $d->name ?>"required>
                    </div>
                    <input type=hidden name="id" value="<?= $d->id ?>"
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Save Domain</button>
                    </div>
                </form>
                <hr>
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
