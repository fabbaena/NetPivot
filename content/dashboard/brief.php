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
require '../model/Netpivot.php';
 
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
        
        $info = new Crud();
        $info->select ='filename';
        $info->from='files';
        $info->condition='uuid="'.$value.'"';
        $info->Read();
        $data = $info->rows;
        foreach ($data as $file){
            $filename = $file['filename'];
        }
        
        $tt = new Crud();
        $tt->select='line';
        $tt->from='details';
        $tt->condition='files_uuid="'.$value.'" AND module="ltm" AND obj_grp="rule" order by line ASC LIMIT 1';
        $tt->Read();
        $tr = $tt->rows;
        
        
        $get_obj = new Crud(); //Total object found into the file
        $get_obj->select='DISTINCT (module)';
        $get_obj->from='details';
        $get_obj->condition='files_uuid="'.$value .'"';
        $get_obj->Read();
        $obj_res = $get_obj->rows;
        $objs = count($obj_res);
        
        $get_rules = new NetPivot();
        $rules = $get_rules->getCounters($value,'ltm','rule');
        
        $get_gslb = new NetPivot();
        $gslb = $get_gslb->getCounters($value,'ltm','gtm');
        
        $get_acl= new NetPivot();
        $acl = $get_acl->getCounters($value,'security','firewall');
        
        $get_aaa = new NetPivot();
        $aaa = $get_aaa->getCounters($value,'apm','aaa');
        
        $modela=new NetPivot(); // Get total lines
        $total = $modela->getTotalLines($value);
        
        $modelb = new NetPivot();
        $ltmD = $modelb->getCNCO($value,'ltm','rule',1);
        $ltmtotal = $ltmD[1] + $ltmD[2];
        $ltmp = ($ltmD[1]*100)/$ltmtotal;
        ?>
        <title>NetPivot</title>  
    </head>
    <body>
    <?php include ('../engine/menu1.php');//Include the first part of the nav bar ?>
    <?php include ('../engine/menu2.php');//Include the second part of the nav bar?>				
    <div class="col-md-1"></div>
    <div class="col-md-10 content">
  
        
        <div class="panel panel-default">
           <div class="panel-body">
               <?php
               
               ?>
               <div class="col-md-6 content">
                <h1 class="projectname"><?php echo $filename;?></h1>
                <br>
               <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">&lt; Go back</a>
               </div>
               
               <div class="col-md-6 content">
                   <br><br>
                
               </div> 
            </div>
            <hr class="divider">
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="col-md-6 content">     
                        <h2 class="filename">Dashboard</h2>
                    </div>
                    <div class="col-md-6">
                        <form method="POST">      
                            <button type="submit" class="btn btn-primary" formaction="modules.php?value=ltm">View Module Details</button>                          
                            <button type="submit" class="btn btn-primary" formaction="objects.php">View Objects</button>                                           
                            <button type="submit" class="btn btn-success" name="uuid" value="<?php echo $value;?>" formaction="../engine/Download.php">Download Target</button>
                        </form>
                    </div>
                </div>    
                <div class="col-md-4">
                    <form method="GET">   
                        <button type="submit" class="btn btn-primary btn-lg glyphicon glyphicon-home"></button><h2 class="nextto"><?php echo $objs;?></h2>
                        <h3 class="tag"><small>MODULES FOUND</small></h3>
                        <button type="submit" class="btn btn-warning btn-lg glyphicon glyphicon-flag" name="value" value="gtm" formaction="modules.php"></button><h2 class="nextto"><?php echo $gslb?></h2>
                        <h3 class="tag"><small>GSLB OBJECTS FOUND</small></h3>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="GET">
                        <button type="submit" class="btn btn-success btn-lg glyphicon glyphicon-signal"></button><h2 class="nextto"><?php echo round($ltmp).'%';?></h2>
                        <h3 class="tag"><small>LTM CONVERSION</small></h3>
                        <button type="submit" class="btn btn-warning btn-lg glyphicon glyphicon-flag" name="value" value="apm" formaction="modules.php"></button><h2 class="nextto"><?php echo $aaa;?></h2>
                        <h3 class="tag"><small>AAA OBJECTS FOUND</small></h3>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="GET">
                        <button type="submit" class="btn btn-danger btn-lg glyphicon glyphicon-flash" name="value" value="rule" formaction="modules.php"></button><h2 class="nextto"><?php echo $rules;?></h2>
                        <h3 class="tag"><small>iRULES CONFIGURED</small></h3>
                        <button type="submit" class="btn btn-warning btn-lg glyphicon glyphicon-flag" name="value" value="security" formaction="modules.php"></button><h2 class="nextto"><?php echo $acl;?></h2>
                        <h3 class="tag"><small>ACLS OBJECTS FOUND</small></h3>
                    </form>
                    <br>
                </div>
                
                <table class="table">
                         <tr class="active">
                            <th style="width: 15%">F5 Module</th>
                            <th style="width: 15%">Total % of Config</th>                                                       
                            <th style="width: 15%">% Converted</th>
                            <th style="width: 15%">% Not Converted</th>
                            <th style="width: 10%">% Omitted</th>
                            <th style="width: 30%">Actions</th> 
                        </tr>
                        <tbody>
                            <?php
                                $model = new Crud();
                                $model->select='DISTINCT (module)';
                                $model->from='details';
                                $model->condition='files_uuid="'.$value.'"';
                                $model->Read();
                                $rows = $model->rows;
                                $total2 = count ($rows);
                                for ($c=0;$c<$total2;$c++){
                                    $data = new NetPivot();
                                    if ($rows[$c][0]=='ltm') {
                                        $info = $data->getCNCO($value, $rows[$c][0],'rule',1);
                                    } else {
                                        $info = $data->getCNCO($value, $rows[$c][0],'',0);
                                    }
                                    $total_data = $info[1] + $info[2];
                                    $t = ($total_data*100)/$total;
                                    $p_c = ($info[1]*100)/$total_data;
                                    $p_nc = ($info[2]*100)/$total_data;
                                    $p_o = ($info[3]*100)/$total_data;
                                    $module = $rows[$c][0];
                                    echo '<tr>';
                                    echo '<td>'.strtoupper($rows[$c][0]).'</td>';
                                    echo '<td class="text-center"><span class="badge badge_bkground_blue_sm">'.round($t).'%</span></td>';
                                    echo '<td class="text-center"><span class="badge badge_bkground_green_sm">'.round($p_c).'%</span></td>';
                                    echo '<td class="text-center"><span class="badge badge_bkground_red_sm">'.round($p_nc).'%</span></td>';
                                    echo '<td class="text-center"><span class="badge badge_bkground_gray_sm">'.round($p_o).'%</span></td>';
                                    echo '<td><a href="text.php?value='.$module.'#line">View Config Text</a>&nbsp;&nbsp;&nbsp;<a href="modules.php?value='.$module.'">View Module Details</a></td>';
                                    echo '</tr>';
                                    if ($rows[$c][0]=='ltm'){
                                        $info = $data->getCNCO($value, $rows[$c][0],'rule',0);
                                        $total_data = $info[1] + $info[2];
                                        $t = ($total_data*100)/$total;
                                        $p_c = ($info[1]*100)/$total_data;
                                        $p_nc = ($info[2]*100)/$total_data;
                                        $p_o = ($info[3]*100)/$total_data;
                                        echo '<tr>';
                                        echo '<td>iRULE</td>';
                                        echo '<td class="text-center"><span class="badge badge_bkground_blue_sm">'.round($t).'%</span></td>';
                                        echo '<td class="text-center"><span class="badge badge_bkground_green_sm">'.round($p_c).'%</span></td>';
                                        echo '<td class="text-center"><span class="badge badge_bkground_red_sm">'.round($p_nc).'%</span></td>';
                                        echo '<td class="text-center"><span class="badge badge_bkground_gray_sm">'.round($p_o).'%</span></td>';
                                        echo '<td><a href="text.php?value=rule&line='.$tr[0]['line'].'#line">View Config Text</a>&nbsp;&nbsp;&nbsp;<a href="modules.php?value=rule">View Module Details</a></td>';
                                        echo '</tr>';
                                    }
                                }
                            ?>
                        </tbody>
                    </table>      
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