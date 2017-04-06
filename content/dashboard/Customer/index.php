<?php
error_reporting(0);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) . '/../../model/StartSession.php';
require_once dirname(__FILE__) . '/../../model/UserList.php';
require_once dirname(__FILE__) . '/../../model/Customer.php';

$session = new StartSession();
$user = $session->get('user');

if (!($user && $user->has_role("Engineer") )) {
    header('location: /');
    exit();
}
$id = $user->id;

?>

<html>
    <head>
        <?php include ('../../engine/css.php'); ?>

        <script language="javascript" src="/js/validator.js"></script>
        <script language="javascript" src="/js/crm.js"></script>
        <script language="javascript" src="/js/customer.js"></script>
        <script language="javascript">
            $().ready(function () {
                $("#new-customer").click(function () {
                    document.location = "create.php";
                });
                $("#return-dashboard").click(function () {
                    document.location = "/dashboard";
                });
                $("#deleteCustomer").click(deleteCustomer);
                loadCustomers(fillTableCustomer);
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
                    <li class="active">Customer Management</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-10 col-md-offset-1 content">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Customers</h4>
                    </div>
                    <div class="panel-body">
                        <button type="submit" id="new-customer" class="btn btn-success">Add Costumer</button>
                        <br><br>
                        <div class="table-responsive">
                            <table class="table table-striped" data-toggle="bootgrid">
                                <thead>
                                    <tr class="active">
                                        <th style="width: 20%">Customer Name</th>
                                        <th style="width: 20%">Phone</th>
                                        <th style="width: 20%">Create Date</th>                        
                                        <th style="width: 10%">More</th>
                                        <th style="width: 10%">Edit</th>
                                        <th style="width: 10%">Delete</th>
                                    </tr>
                                </thead>
                                <tbody id="customerlist">
                                    <tr>
                                        <td colspan="6">
                                            <p class="text-danger" id="nofiles">No customers yet.</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
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

