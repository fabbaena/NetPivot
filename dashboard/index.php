
    <?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/StartSession.php';
require '../model/FileManager.php';
require '../model/Crud.php';
require '../model/ConnectionBD.php';
require '../model/TimeManager.php';
 
$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
$id= $sesion->get('id'); //Get user id
$user_type = $sesion->get('type'); //Get user type = administrator or user
$max_files = $sesion->get('max_files');

if($usuario == false ) {
    header('location:../index.php');
} else {
    ?>

<!DOCTYPE html>
<html lang="en">
    <head>
   
        
        <?php include ('../engine/css.php');//Include links to stylesheets and js scripts ?>
        
         
        <title>NetPivot</title>  
    </head>
    <body>
    
    <?php include ('../engine/menu1.php');//Include the first part of the nav bar ?>
    
	
    <?php include ('../engine/menu2.php');//Include the second part of the nav bar?>				
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
                        <input type="hidden" name="MAX_FILE_SIZE" value="10000000" /> <!--This value establish the size of the file to upload -->
                        <div class="form-group ">
                            <div class="col-md-7 ">                                
                                <input type="file" class="filestyle" name="InputFile" id="InputFile" data-size="lg" required><br>                                                       
                            </div>
                            <div class="col-md-5">
                                <input type="submit" value="Convert" class="btn btn-default btn-lg">                            
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
        <div class="panel-heading">
                <h4>Conversion Manager</h4>
            </div>
            <div class="panel-body">
                <form method="POST">   
                    <div class="table-responsive">
                    <table id="mytable" class="table table-bordred table-striped" data-toggle="bootgrid">
                        <tr class="active">
                           <th>Select</th>
                            <th>File Name</th>                            
                            <th>Upload Date</th>
                        </tr>
                    <tbody>
                    <?php // List all files in the files table per user id
                    
                        try {
                            $list = new Crud();
                            $list->select = '*';
                            $list->from = 'files';
                            $list->condition = "users_id=" . $id ;
                            $list->Read();
                            $files = $list->rows;
                            
                                
                            $done = new Crud();
                            $done->select = 'files_uuid,converted_file,error_file,stats_file';
                            $done->from = 'conversions as c inner join files as f';
                            $done->condition = 'f.uuid=c.files_uuid AND f.users_id=' . $id;
                            $done->Read();
                            $dones = $done->rows;                                   
                            
                           
                            if (empty($files)== false AND empty($dones)== false ) { //   
                                echo '    <div class="col-md-12">
                                          <input type="submit" class="btn btn-danger btn-lg margin-set pull-right" value="Errors" title="Download Error File" formaction="../engine/Download_Errors.php">
                                            <input type="submit" class="btn btn-info btn-lg margin-set pull-right" value="Stats" title="Download Statistics File" formaction="../engine/Download_Stats.php">                                                                                  
                                          <input type="submit" class="btn btn-success btn-lg margin-set pull-right" value="Download" title="Download Converted File" formaction="../engine/Download.php">
                                         <input type="submit" class="btn btn-primary btn-lg margin-set pull-right" value=" View " title="Open Original File" formaction="OpenFile.php">
                                                                                                                             
                                          </div><br><br><br>'; 
                                
                                foreach ($files as $datafiles) { 
                                        $is = false;                                      
                                    foreach ($dones as $ok){                                           
                                            if ($datafiles['uuid']== $ok['files_uuid']) {
                                                $download = $ok['files_uuid'];
                                                $is = true;                                                 
                                            }                                                
                                    }      
                                    if ($is == true) {
                                            echo '<td style="width: 5%"><input type="radio" name="uuid" value="'.$download .'" required />';                                            
                                            echo '<td style="width: 50%"> '.  $datafiles['filename'] . " </td>";
                                            echo '<td style="width: 20%">'. $datafiles['upload_time'] . "</td>";                                            
                                            echo '</tr>';
                                        
                                    } 
                                } 
                            } else {
                                    echo '</div><div class="col-md-12">
                                          <input type="submit" class="btn btn-primary btn-lg margin-set pull-right" value="View" disabled="disabled">
                                          <input type="submit" class="btn btn-success btn-lg margin-set pull-right" value="Download" disabled="disabled">
                                          <input type="submit" class="btn btn-info btn-lg margin-set pull-right" value="Stats" disabled="disabled">
                                          <input type="submit" class="btn btn-danger btn-lg margin-set pull-right" value="Errors" disabled="disabled">
                                         </div><br><br><br>
                                         <p class="text-danger">No files uploaded yet.</p>';
                                }
                                } catch (Exception $ex) {
                                echo '<p class="text-danger">No files uploaded yet</p>';
                            }
                                ?>
                    </tbody>
                    </table>
                    </div>
                </form>
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
<?php
}
?>