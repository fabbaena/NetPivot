<?php

require_once dirname(__FILE__) . '/../../model/StartSession.php';
require_once dirname(__FILE__) . '/../../model/UserList.php';
require_once dirname(__FILE__) . '/../../engine/functions.php';
require_once dirname(__FILE__) . '/../../model/Project.php';

$session = new StartSession();
$user = $session->get('user');
$id = $user->id;

if (!($user && $user->has_role("Engineer"))) {
    header('location: ../');
    exit();
}
$project_id = get_int($_GET, 'projectid');
$project = new Project();
$project->load($project_id);
?>
<!DOCTYPE html>
<html lang="en">
    <head>

        <?php include ('../../engine/css.php'); ?>
        <title>NetPivot</title>
        <script language="javascript" src="/js/validator.js"></script>
        <script language="javascript" src="/js/crm.js"></script>
        <script language="javascript">
            var project_id = <?= $project_id ?>;
            var user_id = <?= $id ?>;
            var customer_id = <?= $project->customerid ?>;
            $().ready(function () {


                $("#return-manage").click(function () {
                    document.location = ".";
                });
                $("#return-dashboard").click(function () {
                    document.location = "/dashboard";
                });
                $("#addline").click(addLine);

                $('#form-project').validator().on('submit', function (e) {
                    if (e.isDefaultPrevented()) {
                        alert('There are errors in the form. Please review the data entered.');
                    } else {
                        var c = new Project(new FormData(document.getElementById("form-project")));
                        c.edit(function(data) {
                            $.bootstrapGrowl(data.message, {
                                type: data.status=='ok'?'success':'danger',
                                delay: 2000,
                            });
                        });
                    }
                    return false;
                });

                loadCustomers(fillSelectCustomer);
                loadMaterials({"projectid": project_id}, fillTableMaterial);

                $("#customerid").change(function () {
                    $('.addcustomer').css('display', 'none');
                    if ($(this).val() == "new") {
                        $('.modal-customer').modal();
                    }

                });

                $("#hwcompare").click(function() {
                    window.open('../comparesm.php','Hardware Compare',
                        "toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=500,height=600");
                });
<?php if (isset($_GET['inserted'])) { ?>
                $.bootstrapGrowl("Quote created", {
                    type: 'success',
                    delay: 2000
                });
<?php } ?>
            });

        </script>
    </head>
    <body>
        <?php include ('../../engine/menu1.php'); ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <ol class="breadcrumb panel-heading">
                        <li><a id="return-dashboard" href="#">Home</a></li>
                        <li><a id="return-manage" href="#">Quote Management</a></li>
                        <li class="active">Edit</li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-10 col-md-offset-1 content">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4>Edit Quote</h4>
                        </div>
                        <div class="panel-body">
                            <form id="form-project" role="form" action="" data-toggle="validator">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="userupdate" id="userupdate" value="<?php echo $user->id ?>" />
                                <input type="hidden" name="total" id="total" value=0 />
                                <input type="hidden" name="projectid" id="cprojectid" value="<?php echo $project->id ?>" />
                                <div class="form-group has-feedback">
                                    <label class="control-label" for="projectname">Project Name:</label>
                                    <input class="form-control" id="projectname" type="text" name="projectname"  pattern="^[.A-z0-9 ']{1,30}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters." value="<?= $project->name ?>" required>
                                    <div class="help-block with-errors"></div>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="control-label" for="description">Description:</label>
                                    <textarea class="form-control" id="description" name="description"  pattern="^[.A-z0-9 ']{1,200}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 30 characters." required><?= $project->description; ?></textarea>
                                    <div class="help-block with-errors"></div>
                                </div>
                                <div class="form-group">
                                    <label for="customerid">Customer</label>
                                    <select class="form-control" name="customerid" id="customerid" ></select>
                                </div>
                                <div class="form-group has-feedback">
                                    <label class="control-label" for="opportunityid">Opportunity ID:</label>
                                    <input class="form-control" id="opportunityid" name="opportunityid" type="text" pattern="^[.A-z0-9 ':/&%]{1,512}$" data-pattern-error="Please use only aphanumeric, apostrophe or space characters only. Max is 512 characters." value="<?= $project->opportunityid ?>" >
                                    <div class="help-block with-errors"></div>
                                </div>
                                <br>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Save Project</button>
                                </div>
                            </form>
                            <hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary" id="hwcompare">Hardware Compare</button>
                                </div>
                                <div class="col-md-offset-8 col-md-2" >
                                    <button type="button" class="btn btn-success" id="addline">Add Lines</button>
                                </div>
                            </div>
                            <table class="table table-bordred table-striped">
                                <thead>
                                    <tr>
                                        <th>Sku</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total Price</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody id="materiallist"></tbody>
                            </table>
                            <div class="row">
                                <div class="col-md-2 col-md-offset-8">Total</div>
                                <div class="col-md-2" id="totalval"></div>
                            </div>
                        </div>
                    </div>
                </div>    
            </div>

            <?php include ("modalMaterial.inc"); ?>
            <?php include ("modalCustomer.inc"); ?>
        </div>
        <footer class="pull-left footer">
            <p class="col-md-12">
            <hr class="divider">
            </p>
        </footer>
    </body>
</html>


