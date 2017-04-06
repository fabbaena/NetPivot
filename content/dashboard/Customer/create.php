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
$id = $user->id;

?>

<html>
    <head>
        <?php include ('../../engine/css.php'); ?>

        <script language="javascript" src="/js/validator.js"></script>
        <script language="javascript" src="/js/crm.js"></script>
        <script language="javascript">
            var idcustomer = "";
            $().ready(function () {
                $("#return-default").click(function () {
                    document.location = ".";
                });
                $("#return-dashboard").click(function () {
                    document.location = "/dashboard";
                });

                $('#form-customer').validator().on('submit', function (e) {
                    if (e.isDefaultPrevented()) {
                        alert('There are errors in the form. Please review the data entered.');
                    } else {
                        var c = new Customer(new FormData(document.getElementById("form-customer")));
                        c.create(function(data) {
                            document.location = "edit.php?customerid=" + data.idInsert + "&inserted=1";
                        });
                    }
                    return false;
                });
            })


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
                        <li><a id="return-default" href="#">Customer Management</a></li>
                        <li class="active">Add Customer</li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-10 col-md-offset-1 content">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>New Customer</h4>
                        </div>
                        <div class="panel-body">

                            <form id="form-customer" role="form" action="" data-toggle="validator">
                                <input type="hidden" name="action" value="create">
                                <input type="hidden" name="usercreate" value="<?php echo $id ?>" />
                                <div class="form-group has-feedback">
                                    <label class="control-label" for="CustomerName">Name:</label>
                                    <input class="form-control" id="CustomerName" type="text" name="CustomerName"  pattern="^[.A-z0-9 ']{1,30}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters." required>
                                    <div class="help-block with-errors"></div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="control-label" for="Phone">Phone:</label>
                                    <input class="form-control" id="Phone" type="text" name="Phone" placeholder="222-22-22 ext 123" pattern="^[.A-z0-9 ']{1,30}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters." required>
                                    <div class="help-block with-errors"></div>
                                </div>

                                <br>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Add Customer</button>
                                </div>
                            </form>
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

