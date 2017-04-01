<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE);
require_once dirname(__FILE__) . '/../../model/StartSession.php';
require_once dirname(__FILE__) . '/../../model/UserList.php';
require_once dirname(__FILE__) . '/../../engine/functions.php';
require_once dirname(__FILE__) . '/../../model/Customer.php';
require_once dirname(__FILE__) . '/../../model/Contact.php';

$session = new StartSession();
$user = $session->get('user');

if (!($user && $user->has_role("Engineer") )) {
    header('location: /');
    exit();
}

$customer_id = get_int($_GET, 'customerid');
$customer = new Customer();
$customer->load($customer_id);

$cl = new ContactList($customer_id);
?>
<!DOCTYPE html>
<html lang="en">
    <head>

        <?php include ('../../engine/css.php'); ?>
        <title>NetPivot</title>
        <script language="javascript" src="/js/validator.js"></script>
        <script language="javascript">
            $().ready(function () {
                $("#return-default").click(function () {
                    document.location = ".";
                });
                $("#return-dashboard").click(function () {
                    document.location = "/dashboard";
                });
                $("#return-customer").click(function () {
                    document.location = ".";
                });

            });

        </script>
    </head>
    <body >
        <?php include ('../../engine/menu1.php'); ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <ol class="breadcrumb panel-heading">
                        <li><a id="return-dashboard" href="#">Home</a></li>
                        <li><a id="return-default" href="#">Customer Management</a></li>
                        <li class="active">Customer Details</li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-10 col-md-offset-1 content">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>Customer Details (<?= $customer->id ?>)</h4>
                        </div>
                        <div class="panel-body">
                            <div class="pull-right">
                                <button type="submit" id="return-customer" class="btn btn-danger">Back</button>
                            </div>
                            <div class="form-group">
                                <label class="control-label" >Name:</label>
                                <p class="help-block"><?= $customer->name ?></p>                          
                            </div>
                            <div class="form-group">
                                <label class="control-label" >Phone:</label>
                                <p class="help-block"><?= $customer->phone ?></p>                            
                            </div>
                            <div class="form-group">
                                <label class="control-label" >Create Date:</label>
                                <p class="help-block"><?= $customer->createdate ?></p>                            
                            </div>
                            <div class="form-group">
                                <label class="control-label" >Create Update:</label>
                                <p class="help-block"><?= $customer->userucreate ?></p>                            
                            </div>
                            <br>
                            <hr>
                            <h3>Associated contacts</h3>
                            <table class="table table-bordred table-striped">
                                <tr>
                                    <th>Contact name</th>
                                    <th>Position</th>
                                    <th>Phone</th>
                                    <th>Create Date</th>                        
                                    <th>Update Date</th>                            
                                </tr>
                                <?php foreach ($cl->Contacts as $contact_data) { ?>
                                    <tr>
                                        <td style="width: 20%"><?= $contact_data->name ?></td>
                                        <td style="width: 20%"><?= $contact_data->position ?></td>
                                        <td style="width: 20%"><?= $contact_data->phone ?></td>
                                        <td style="width: 20%"><?= $contact_data->createdate ?></td>
                                        <td style="width: 20%"><?= $contact_data->updatedate ?></td>

                                    </tr>

                                <?php } //close foreach  ?>
                            </table>
                            <?php if(count($cl->Contacts) == 0) { ?>
                                <p class="text-danger" id="nofiles">No contacts yet.</p>
                            <?php } ?>

                        </div>
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


