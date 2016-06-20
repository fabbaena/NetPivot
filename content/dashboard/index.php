
    <?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/StartSession.php';
require '../model/Crud.php';
require '../model/TimeManager.php';
 
$sesion = new StartSession();
$usuario = $sesion->get('usuario');
$id= $sesion->get('id'); 
$user_type = $sesion->get('type'); 
$max_files = $sesion->get('max_files');
$sesion->set('filename', '');
$roles = $sesion->get('roles');

if($usuario == false ) { 
    header('location: /'); 
    exit();
}

$list = new Crud();
$list->select = '*';
$list->from = 'files';
$list->condition = "users_id=" . $id . ' order by upload_time DESC';
$list->Read();
$files = $list->rows;

$done = new Crud();
$done->select = 'files_uuid,converted_file,error_file,stats_file,time_conversion';
$done->from = 'conversions as c inner join files as f';
$done->condition = 'f.uuid=c.files_uuid AND f.users_id=' . $id ;
$done->Read();
$dones = $done->rows;

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include ('../engine/css.php');?>
        <title>NetPivot</title>  
    </head>
    <body>
    <?php include ('../engine/menu1.php');?>
    <div class="container-fluid">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10 content">
            <div class="alert alert-success fade-in">  
                <p class="text-success"><strong>Welcome!</strong> NetPivot conversion magic is happening with F5 version 11 configuration files only.</p>
                </div>  
            
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>F5 Configuration Converter</h4>
                </div>
                <div class="panel-body">
                    <form enctype="multipart/form-data" action='../engine/uploader.php?id='<?php $id;?> method="POST">
                        <div class="form-group">
                            <div class="col-sm-9 ">                                
                                <input type="file" class="filestyle" name="InputFile" id="InputFile" data-size="lg" required><br>
                            </div>
                            <div class="col-sm-3">
                                <input type="submit" value="Convert" class="btn btn-default btn-lg" >                            
                            </div>  
                            <div class="col-sm-9"></div>
                            <div class="col-sm-3">
                                <input id="Orphan" type="checkbox" checked autocomplete="off"> 
                                Convert Orphan Objects
                            </div>
                        </div>    
                        <?php 
                            if (isset($_GET['exist_file'])) {
                                $fallado = $_GET['exist_file'];
                                echo '<p class="text-danger">File '.$fallado .' already exists or exceed the maximum file size</p>';
                            }
                            if (isset($_GET['upload_error'])) {
                                $fallado = $_GET['upload_error'];
                                echo '<p class="text-danger">An error ocurred uploading the file, please try again or contact your administrator.</p>';
                            }
                        ?>                                               
                    </form><br>                    
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading"><h4>Conversion Manager</h4></div>
                <div class="panel-body">
                    <form method="POST">   
                        <div class="table-responsive">
                        <table id="mytable" class="table table-bordred table-striped" data-toggle="bootgrid">
                            <tr class="active">
                               <th>Select</th>
                                <th>File Name</th>                            
                                <th>Options</th>
                                <th>Upload Date</th>
                                
                            </tr>
                        <tbody>
                        <?php 
                                if (!empty($files) AND !empty($dones) ) { ?>
                                    <div class="col-md-12">
                                        <input type="submit" class="btn btn-primary btn-lg margin-set pull-right" value="View Conversion" title="View Conversion" formaction="content.php">                                                                                  
                                    </div><br><br><br>
                                    <?php
                                    foreach ($files as $datafiles) { 
                                            $is = false;                                      
                                        foreach ($dones as $ok){                                           
                                                if ($datafiles['uuid']== $ok['files_uuid']) {
                                                    $download = $ok['files_uuid'];
                                                    $is = true;                                                 
                                                }                                                
                                        }      
                                        if ($is == true) { ?>
                                        <tr>
                                            <td style="width: 5%"><input type="radio" name="uuid" value="<?= $download ?>" required /></td>
                                            <td style="width: 55%"><?= $datafiles['filename'] ?></td>
                                            <td style="width: 20%">
                                                <a href="rename.php?file=<?= $datafiles['uuid'] ?>">Rename</a>&nbsp;
                                                <a href="reprocess.php?file=<?= $datafiles['uuid'] ?>">Reprocess</a>
                                            </td>
                                            <td style="width: 20%"><?= $datafiles['upload_time'] ?></td>
                                        </tr>
                                            <?php
                                        } 
                                    } 
                                } else { ?>
                                    <div class="col-md-12">
                                        <input type="submit" class="btn btn-primary btn-lg margin-set pull-right" value="View Conversion" title="View Conversion" disabled="disabled">          
                                             </div><br><br><br>
                                             <p class="text-danger">No files uploaded yet.</p>
                                <?php } ?>
                        </tbody>
                        </table>
                        </div>
                    </form>
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
