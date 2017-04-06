<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) . '/../../model/StartSession.php';
require_once dirname(__FILE__) . '/../../model/UserList.php';
require_once dirname(__FILE__) . '/../../model/Project.php';
require_once dirname(__FILE__) . '/../../model/Customer.php';


$session = new StartSession();
$user = $session->get('user');

if (!($user && $user->has_role("Engineer") )) {
    header('location: /');
    exit();
}
$id = $user->id;

$customerlist = new CustomerList(array('usercreate' => $user->id));
?>

<html>
    <head>
        <?php include ('../../engine/css.php'); ?>


        <script language="javascript" src="/js/validator.js"></script>
        <script language="javascript" src="/js/crm.js"></script>
        <script language="javascript">
            var customer_id = 0;
            var userid = <?= $user->id ?>;
            $().ready(function () {
                $("#return-default").click(function () {
                    document.location = ".";
                });
                $("#return-dashboard").click(function () {
                    document.location = "/dashboard";
                });

                $('#form-project').validator().on('submit', function (e) {
                    if (e.isDefaultPrevented()) {
                        alert('There are errors in the form. Please review the data entered.');
                    } else {
                        var c = new Project(new FormData(document.getElementById("form-project")));
                        c.create(function(data) {
                            document.location = "edit.php?projectid=" + data.idInsert + "&inserted=1";
                        });
                    }
                    return false;
                });
                $("#customerid").change(function () {
                    if ($(this).val() == "new") {
                        $(".modal-customer").modal();
                    }

                })
                loadCustomers(fillSelectCustomer);
            })



        </script>
        <title>NetPivot - Customer Management</title>  
    </head>
    <?php include ('../../engine/menu1.php'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <ol class="breadcrumb panel-heading">
                    <li><a id="return-dashboard" href="#">Home</a></li>
                    <li><a id="return-default" href="#">Quote Management</a></li>
                    <li class="active">New</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-10 col-md-offset-1 content">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>New Quote</h4>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" role="form" action="" data-toggle="validator">
                            <input type="hidden" name="action" value="create">
                            <input type="hidden" name="usercreate" id="usercreate" value="<?php echo $id ?>" />
                            <div class="form-group has-feedback">
                                <label class="control-label" for="projectname">Project Name:</label>
                                <input class="form-control" id="projectname" type="text" name="projectname"  pattern="^[.A-z0-9 ']{1,30}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters." required>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group has-feedback">
                                <label class="control-label" for="description">Description:</label>
                                <textarea class="form-control" id="description" name="description"  pattern="^[.A-z0-9 ']{1,200}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters." required></textarea>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <label for="xttachment">Attachment </label>
                                <input type="file" id="attachment" name="attachment">
                            </div>
                            <div class="form-group">
                                <label for="xttachment">Customer</label>
                                <select class="form-control" name="customerid" id="customerid" ></select>
                            </div>
                            <br>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                            <div class="form-group addMaterials pull-right" style="display: none">
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target=".bs-example-modal-materials">Add Lines</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <?php include('modalCustomer.inc'); ?>

    <footer class="pull-left footer">
        <p class="col-md-12">
        <hr class="divider">
        </p>
    </footer>

</html>

