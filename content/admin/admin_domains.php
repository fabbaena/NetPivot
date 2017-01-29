<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/DomainList.php';
 
$session = new StartSession();
$user = $session->get('user');

if(!($user && $user->has_role("System Admin"))) {
    header('location: ../');
    exit();
}

$domainlist = new DomainList();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('../engine/css.php');//Include links to stylesheets and js scripts ?>
        <title>NetPivot - Domain Administration</title>
        <script language="javascript">
        $().ready( function() {
            $("#adminconsole").click(function() {document.location="./";});
            $("#btn-newdomain").click(function() { document.location="new_domain.php";});
            })
        </script>
    </head>
    <body >
    <?php include ('../engine/menu1.php');//Include the first part of the nav bar ?>
    <div class="col-md-1"></div>
    <div class="col-md-10 content">
        <div class="panel panel-default">
            <ol class="breadcrumb panel-heading">
                <li><a id="adminconsole" href="#">Admin Console</a></li>
                <li class="active">Company Management</li>
            </ol>
            <div class="panel-body">
                <?php 
                    if(isset($_GET['new_done'])) {
                        echo '<p class="text-primary">Domain created correctly</p>';
                    } elseif(isset($_GET['user_exists'])) {
                        echo '<p class="text-danger">Domain exists already</p>';
                    } elseif(isset($_GET['new_error'])) {
                        echo '<p class="text-warning">Error trying to create domain, please try again</p>';
                    } elseif(isset($_GET['mod_ok'])) {
                        echo '<p class="text-primary">Domain modified correctly</p>';
                     } elseif(isset($_GET['mod_error'])) {
                        echo '<p class="text-primary">Error modifing domain settings, please try again.</p>';
                     } elseif(isset($_GET['delete_ok'])) {
                        echo '<p class="text-primary">Domain deleted correctly.</p>';
                     } elseif(isset($_GET['delete_error'])) {
                        echo '<p class="text-primary">Error deleting domain, please try again.</p>';
                     }
                ?>
                <div class="btn btn-default" id="btn-newdomain">New Company</div>
                <h4>Companies Created</h4>
                <table class="table table-bordred table-striped">
                    <tr>
                        <th>Company</th>
                        <th>Domain Name</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                    <?php foreach ($domainlist->list as $domain_data) { ?>
                    <tr>
                        <td style="width: 40%"><?= $domain_data->name ?></td>
                        <td style="width: 40%"><?= $domain_data->domain ?></td>
                        <td style="width: 10%">
                            <p data-placement="top" title="Modify domain settings">
                                <a href="modify_domain.php?id=<?= $domain_data->id ?>" class="btn btn-warning btn-xs" role="button">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>
                            </p>
                        </td>
                        <td style="width: 10%">
                            <p data-placement="top" title="Delete">
                                <a href="../engine/delete_domain.php?id=<?= $domain_data->id ?>" class="btn btn-success btn-xs" role="button">
                                    <span class="glyphicon glyphicon-trash"></span>
                                </a>
                            </p>
                        </td>
                    </tr>
                    <?php } //close foreach ?>
                </table>
                </div>
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