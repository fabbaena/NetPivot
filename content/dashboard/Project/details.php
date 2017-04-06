<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require_once dirname(__FILE__) . '/../../model/StartSession.php';
require_once dirname(__FILE__) . '/../../model/UserList.php';
require_once dirname(__FILE__) . '/../../engine/functions.php';
require_once dirname(__FILE__) . '/../../model/Project.php';
require_once dirname(__FILE__) . '/../../model/Customer.php';
require_once dirname(__FILE__) . '/../../model/Material.php';

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
$customerlist = new CustomerList();
$materiallist = new MaterialList($project_id);
?>
<!DOCTYPE html>
<html lang="en">
    <head>

        <?php include ('../../engine/css.php'); ?>
        <title>NetPivot</title>

        <script language="javascript">
            $().ready(function () {

                $("#return-manage").click(function () {
                    document.location = ".";
                });
                $("#return-dashboard").click(function () {
                    document.location = "/dashboard";
                });
                $("#return-project").click(function () {
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
                    <li><a id="return-manage" href="#">Project Management</a></li>
                    <li class="active">Project Details</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-10 col-md-offset-1 content">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4>Project Details (<?= $project->id ?>)</h4>
                    </div>
                    <div class="panel-body">
                        <div class="pull-right">
                            <button type="submit" id="return-project" class="btn btn-danger">Back</button>
                        </div>
                        <div class="form-group">
                            <label class="control-label" >Project Name:</label>
                            <p class="help-block" ><?= $project->name ?></p>                            
                        </div>
                        <div class="form-group has-feedback">
                            <label class="control-label" >Description:</label>
                            <p class="help-block"><?php echo $project->description; ?></p>                            
                        </div>
                        <div class="form-group">
                            <label class="control-label">Customer</label>
                            <p class="help-block">
                                <?= $project->customername ?>
                            </p>
                        </div>
                        <div class="form-group has-feedback">
                            <label class="control-label" >Create data:</label>
                            <p class="help-block"><?php echo $project->createdate; ?></p>                            
                        </div>
                        <div class="form-group has-feedback">
                            <label class="control-label" >Update data:</label>
                            <p class="help-block"><?php echo $project->updatedate; ?></p>                            
                        </div>
                        <hr>
                        <h3>Materials</h3>
                        <table class="table table-bordred table-striped">
                            <tr>
                                <th>Sku</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                            <?php foreach ($materiallist->Materials as $material_data) { ?>
                                <tr>
                                    <td style="width: 20%"><?= $material_data->sku ?></td>
                                    <td style="width: 20%"><?= $material_data->description ?></td>
                                    <td style="width: 20%"><?= $material_data->quantity ?></td>
                                    <td style="width: 20%"><?= $material_data->price ?></td>                                
                                </tr>

                            <?php } ?>
                        </table>
                        <?php if(count($materiallist->Materials) == 0) { ?>
                        <p class="text-danger" id="nofiles">No lines yet.</p>
                        <?php } ?>
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


