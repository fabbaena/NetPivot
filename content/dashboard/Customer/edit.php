<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) . '/../../model/StartSession.php';
require_once dirname(__FILE__) . '/../../model/UserList.php';
require_once dirname(__FILE__) . '/../../engine/functions.php';
require_once dirname(__FILE__) . '/../../model/Customer.php';

$session = new StartSession();
$user = $session->get('user');
$id = $user->id;

if (!($user && $user->has_role("Engineer") )) {
    header('location: /');
    exit();
}
$customer_id = get_int($_GET, 'customerid');
$customer = new Customer();
$customer->load($customer_id);
?>
<!DOCTYPE html>
<html lang="en">
    <head>

        <?php include ('../../engine/css.php'); ?>
        <title>NetPivot</title>
        <script language="javascript" src="/js/validator.js"></script>
        <script language="javascript" src="/js/crm.js"></script>
        <script language="javascript">
            var customerid = <?= $customer->id ?>;
            var userid = <?= $user->id ?>;

            $().ready(function () {
                $("#return-default").click(function () {
                    document.location = ".";
                });
                $("#return-dashboard").click(function () {
                    document.location = "/dashboard";
                });
                $("#add-contact-btn").click(function() {
                    $("#modal-contact-action").val("create");
                    $(".modal-contact").modal();
                });
                $("#add-project-btn").click(function() {
                    $(".modal-project").modal();
                });

                $('#form-customer').validator().on('submit', function (e) {
                    if (e.isDefaultPrevented()) {
                        alert('There are errors in the form. Please review the data entered.');
                    } else {
                        var c = new Customer(new FormData(document.getElementById("form-customer")));
                        c.edit(function(data) {
                            $.bootstrapGrowl(data.message, {
                                type: data.status=='ok'?'success':'danger',
                                delay: 2000,
                            });
                        });
                    }
                    return false;
                });


                loadContacts({'customerid': customerid }, fillTableContact);
                loadProjects({'customerid': customerid }, fillTableProject);
            })

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
                        <li class="active">Modify Customer</li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-10 col-md-offset-1 content">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>Edit Customer</h4>
                        </div>
                        <div class="panel-body">
                            <form id="form-customer" role="form" action="" data-toggle="validator">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="userupdate" value="<?php echo $user->id ?>" />
                                <input type="hidden" name="customerid" id="customerid" value="<?php echo $customer->id ?>" />
                                <div class="form-group has-feedback">
                                    <label class="control-label" for="CustomerName">Name:</label>
                                    <input class="form-control" id="CustomerName" type="text" name="CustomerName"  pattern="^[.A-z0-9 ']{1,100}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 100 characters." value="<?= $customer->name ?>" required>
                                    <div class="help-block with-errors"></div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="control-label" for="phone">Phone:</label>
                                    <input class="form-control" id="phone" type="text" name="Phone" placeholder="222-22-22 ext 123" pattern="^[.A-z0-9 ']{1,30}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters." value="<?= $customer->phone ?>" required>
                                    <div class="help-block with-errors"></div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Save customer</button>
                                </div>
                            </form>
                            <hr>
                            <div class="pull-right">
                                <button type="button" id="add-contact-btn" class="btn btn-primary">add contact</button>
                            </div>
                            <table class="table table-bordred table-striped">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Phone</th>
                                    <th style="width: 100px">Create Date</th>
                                    <th style="width: 60px">Edit</th>
                                    <th style="width: 60px">Delete</th>
                                </tr>
                                </thead>
                                <tbody id="contactlist">
                                    <td colspan="6">
                                        <p class="text-danger" id="nofiles">No contacts yet.</p>
                                    </td>
                                </tbody>
                            </table>
                            <hr>
                            <div class="pull-right">
                                <button type="button" id="add-project-btn" class="btn btn-primary">add Quote</button>
                            </div>
                            <table class="table table-bordred table-striped">
                                <thead>
                                <tr>
                                <th >Quote name</th>
                                <th >Customer</th>  
                                <th >Total</th>
                                <th style="width: 60px">Edit</th>
                                <th style="width: 60px">Delete</th>
                                </tr>
                                </thead>
                                <tbody id="projectlist">
                                    <td colspan="6">
                                        <p class="text-danger" id="nofiles">No quotes yet.</p>
                                    </td>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>    
        </div>
<?php include('modalContact.inc'); ?>
<?php include('modalProject.inc'); ?>
        <footer class="pull-left footer">
            <p class="col-md-12">
            <hr class="divider">
            </p>
        </footer>
    </body>
</html>


