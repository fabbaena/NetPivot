<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) . '/../../model/StartSession.php';
require_once dirname(__FILE__) . '/../../model/UserList.php';

$session = new StartSession();
$user = $session->get('user');

if (!($user && $user->has_role("Engineer") )) {
    header('location: /');
    exit();
}
?>

<html>
    <head>
        <?php include ('../../engine/css.php'); ?>

        <script language="javascript" src="/js/validator.js"></script>
        <script language="javascript" src="/js/crm.js"></script>
        <script language="javascript" src="/js/project.js"></script>
        <script language="javascript">
            $().ready(function () {
                $("#new-project").click(function () {
                    document.location = "create.php";
                });
                $("#return-dashboard").click(function () {
                    document.location = "/dashboard";
                });
                $("#deleteProject").click(deleteProject);
                loadProjects({}, fillTableProject);
            });
        </script>
        <title>NetPivot - Customer Management</title>  
    </head>
    <body>
    <?php include ('../../engine/menu1.php'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <ol class="breadcrumb panel-heading">
                    <li><a id="return-dashboard" href="#">Home</a></li>
                    <li class="active">Quote Management</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-10 col-md-offset-1 content">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Quotes</h4>
                    </div>
                    <div class="panel-body">
                        <button type="submit" id="new-project" class="btn btn-success">New Quote</button>
                        <table class="table table-bordred table-striped">
                            <thead>
                            <tr>
                                <th>Quote name</th>
                                <th>Customer</th>  
                                <th>Total</th>
                                <th style="width: 60px">Edit</th>
                                <th style="width: 60px">Delete</th>
                            </tr>
                            </thead>
                            <tbody id="projectlist">
                                <tr>
                                    <td colspan="7">
                                        <p class="text-danger" id="nofiles">No quotes yet.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include('modalConfirmation.inc'); ?>
    <footer class="pull-left footer">
        <p class="col-md-12">
        <hr class="divider">
        </p>
    </footer>
    </body>
</html>

