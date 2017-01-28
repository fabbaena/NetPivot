<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/DomainList.php';
require_once dirname(__FILE__) .'/../model/UserList.php';

$session = new StartSession();
$user = $session->get('user');
if(!($user && $user->has_role("System Admin"))) {
    header('location: /'); 
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
        <title>NetPivot - Company Administration</title>
        <script language="javascript" src="/js/validator.js"></script>
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
                <li><a class="domainmanagement" href="#">Company Management</a></li>
                <li class="active">Modify Company</li>
            </ol>
            <div class="panel-body">
                <h4>Modify</h4><hr >
                <form class="form-submit" role="form" data-toggle="validator" method="POST" action="../engine/modify_domain.php">
                    <div class="form-group has-feedback">
                        <label class="control-label" for="name">Company Name:</label>
                        <input class="form-control" id="name" type="text" name="name" pattern="^[._A-z0-9 \-',]{3,}$" data-pattern-error="Invalid character. Use letters, numbers and the following characters (._-',)" value="<?= $d->name ?>" required>
                        <div class="help-block with-errors"></div>
                    </div>
                    <div class="form-group has-feedback">
                        <label class="control-label" for="domain">Domain Name:</label>
                        <input class="form-control" id="domain" type="text" name="domain" pattern="^[._A-z0-9]{1,}\.[_A-z0-9]{1,}$" data-pattern-error="Please use a valid domain. No spaces" value="<?= $d->domain ?>" required>
                        <div class="help-block with-errors"></div>
                    </div>
                    <input type=hidden name="id" value="<?= $d->id ?>"
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Save</button>
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
