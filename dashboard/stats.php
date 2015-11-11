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
   
        
        <?php 
        include ('../engine/css.php');//Include links to stylesheets and js scripts 
        if (isset($_POST['uuid'])) {
             $value = htmlspecialchars($_POST['uuid']);
             $sesion->set('uuid', $value);
        } else {
            $value = $sesion->get('uuid');
        }
        
        
        $model = new Crud();
        $model->select ='filename';
        $model->from ='files';
        $model->condition = "uuid='" . $value . "'";
        $model->Read();
        $file = $model->rows;
        foreach ($file as $filen){
            $filename = $filen['filename'];
        }
        $target = $value . "_" . $filename . ".conf";
        $file = 'files/'.$value . '_stats.txt';
        $model2 = new FileManager();
        $stats = $model2->GetNumbers($file); 
        
        foreach ($stats as $get_numbers){
            $total_lines = $get_numbers[1];
            $converted_lines = $get_numbers[3];
        }
        
        $percent = ($converted_lines * 100)/ $total_lines;
        $csv = 'files/'.$value.'_stats.csv';
        

        
        ?>
        
         
        <title>NetPivot</title>  
    </head>
    <body>
    
    <?php include ('../engine/menu1.php');//Include the first part of the nav bar ?>
    
	
    <?php include ('../engine/menu2.php');//Include the second part of the nav bar?>				
    <div class="col-md-1"></div>
    <div class="col-md-10 content">
  
        
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Conversion Statistics<br><small><strong><?php echo $filename?></strong></small></h4>
            </div>
            
            <div class="panel-body">
                <div class="col-md-3">
                    <h1 class="number text-center"><?php echo round($percent) .'%';?></h1>
                    <H4 class="text-center text-muted">CONVERSION</h4>
                </div>
                <div class="col-md-9 content">
                    <p class="text-muted">F5 to Netscaler Conversion Summary</p>
                    <table class="table table-striped">
                         <tr class="active">
                             <th style="width: 25%"></th>
                           <th style="width: 25%">Total</th>
                            <th style="width: 25%">Converted</th>                            
                            <th style="width: 25%">Skipped</th>
                        </tr>
                        <tbody>
                    <?php
                       
                        foreach ($stats as $data){
                            echo '<tr>'
                        . '<td><strong>Characters</strong></td><td>'.$data[0].'</td><td>'.$data[2].'</td><td>'.$data[4].'</td></tr>'
                        . '<tr><td><strong>Lines</strong></td><td>'.$data[1].'</td><td>'.$data[3].'</td><td>'.$data[5].'</td></tr>';
                            
                        }
                          ?>
                        </tbody>
                        </table>
                </div>
                <div class="col-md-12 content">
                    <p class="text-muted">Object Statistics</p>
                    <table class="table">
                         <tr class="active">
                            <th style="width: 25%">Object Name</th>
                            <th style="width: 25%">Count</th>
                            <th style="width: 25%"># Characters</th>                            
                            <th style="width: 25%">#Lines</th>
                        </tr>
                        <tbody>
                        <?php
                        echo '<tr>';
                        if (($handle = fopen($csv, "r")) !== FALSE) {
                            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                                $num = count($data);                             
                                
                                for ($c=0; $c < $num; $c++) {
                                    if ($c !=1){
                                    echo '<td>'.$data[$c] .'</td>';
                                }
                                }
                                echo '</tr>';
                            }
                        }
                       
                        fclose($handle);
                           /*foreach ($stats as $data) {
                           //    echo ''
                               . '<tr><td>Monitors</td><td>'.$data[6].'</td><td>'.$data[7].'</td><td>'.$data[8].'</tr>'
                               . '<tr><td>Nodes</td><td>'.$data[9].'</td><td>'.$data[10].'</td><td>'.$data[11].'</tr>'
                               . '<tr><td>Pools</td><td>'.$data[12].'</td><td>'.$data[13].'</td><td>'.$data[14].'</tr>'
                               . '<tr><td>Virtuals</td><td>'.$data[15].'</td><td>'.$data[16].'</td><td>'.$data[17].'</tr>'
                               . '<tr><td>Virtual Addresses</td><td>'.$data[18].'</td><td>'.$data[19].'</td><td>'.$data[20].'</tr>'
                               . '<tr><td>Profiles</td><td>'.$data[21].'</td><td>'.$data[22].'</td><td>'.$data[23].'</tr>'
                               . '<tr><td>Persistences</td><td>'.$data[24].'</td><td>'.$data[25].'</td><td>'.$data[26].'</tr>';
                           }*/
                        ?>
                        </tbody>
                    </table>
                    <form method="POST">
                        <button type="submit" class="btn btn-default btn-lg" formaction="OpenFile.php" name="uuid"  value="<?php echo $value; ?>">View Source File</button>                        
                        
                        
                        <button type="submit" class="btn btn-success btn-lg margin-set pull-right" name="uuid" value="<?php echo $value;?>" formaction="../engine/Download.php">Download Target File</button>
                        <button type="submit" class="btn btn-primary btn-lg margin-set pull-right" name="uuid" value="<?php echo $target;?>" formaction="OpenTFile.php">View Target File </button>
                        <button type="submit" class="btn btn-danger btn-lg margin-set pull-right" name="uuid" value="<?php echo $value;?>" formaction="../engine/Open_ErrorFile.php">View Skipped</button>
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
<?php
}
?>