<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
 
$session = new StartSession();
$user = $session->get('user');


if(!($user && $user->has_role("System Admin"))) {
    header('location: ../');
    exit();
}

$userlist = new UserList();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('../engine/css.php');//Include links to stylesheets and js scripts ?>
        <title>NetPivot - User Administration</title>
        <script language="javascript">
        $().ready( function() {
            $("#adminconsole").click(function() {document.location="./";});
            $("#btn-newuser").click(function() {document.location="new_user.php";});
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
                <li class="active">User Management</li>
            </ol>
            <div class="panel-body">
                <?php 
                    if(isset($_GET['new_done'])) {
                        echo '<p class="text-primary">User created correctly</p>';
                    } elseif(isset($_GET['user_exists'])) {
                        echo '<p class="text-danger">User exists already</p>';
                    } elseif(isset($_GET['new_error'])) {
                        echo '<p class="text-warning">Error trying to create user, please try again</p>';
                    } elseif(isset($_GET['mod_ok'])) {
                        echo '<p class="text-primary">User modified correctly</p>';
                     } elseif(isset($_GET['mod_error'])) {
                        echo '<p class="text-primary">Error modifing user settings, please try again.</p>';
                     } elseif(isset($_GET['delete_ok'])) {
                        echo '<p class="text-primary">User deleted correctly.</p>';
                     } elseif(isset($_GET['delete_error'])) {
                        echo '<p class="text-primary">Error deleting user, please try again.</p>';
                     }
                ?>
                <div class="btn btn-default" id="btn-newuser">New User</div>
                <h4>Users Created</h4>
                <table class="table table-bordred table-striped">
                    <tr>
                        <th>Username</th>
                        <th>Roles</th>
                        <th>Max Files</th>
                        <th>Max Conversions</th>
                        <th>More</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                    <?php foreach ($userlist->users as $user_data) { ?>
                    <tr>
                        <td style="width: 20%"><?= $user_data->name ?></td>
                        <td style="width: 20%"><?= implode(", ", $user_data->getRoleList()) ?></td>
                        <td style="width: 20%"><?= $user_data->getMaxFiles() ?></td>
                        <td style="width: 20%"><?= $user_data->getMaxConversions() ?></td>
                        <td style="width: 10%">
                            <p data-placement="top" title="User details">
                                <a href="info_user.php?id=<?= $user_data->id ?>" class="btn btn-primary btn-xs" role="button">
                                    <span class="glyphicon glyphicon-eye-open"></span>
                                </a>
                            </p>
                        </td>
                        <td style="width: 10%">
                            <p data-placement="top" title="Modify user settings">
                                <a href="modify_user.php?id=<?= $user_data->id ?>" class="btn btn-warning btn-xs" role="button">
                                    <span class="glyphicon glyphicon-pencil"></span>
                                </a>
                            </p>
                        </td>
                        <td style="width: 10%">
                        <?php if ($user_data->id != 1) { ?>
                                <p data-placement="top" title="Delete">
                                    <a href="../engine/delete_user.php?id=<?= $user_data->id ?>" class="btn btn-success btn-xs" role="button">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                </p>
                        <?php  }  ?>
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