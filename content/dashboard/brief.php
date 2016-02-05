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
        $get_obj->condition='files_uuid="'.$value .'" ';
        $get_obj->Read();
        $obj_res = $get_obj->rows;
        $objs = count($obj_res);
        
        $get_rules = new NetPivot();
        $rules = $get_rules->getCounters($value,'ltm','rule');
        
        $get_gslb = new NetPivot();
        $gslb = $get_gslb->getCounters($value,'ltm','gtm');
        
        $get_acl= new NetPivot();
        $acl = $get_acl->getCounters($value,'asm','');
        
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
               <?php include('../engine/breadcrumbs.php');//Includethe breadcrumb ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-6 content">
                        <h2 class="filename">Dashboard</h2>
                    </div>
                    <div class="col-xs-6">
                        <form method="POST">      
                            <button type="submit" class="btn btn-primary" formaction="modules.php?value=ltm">View Module Details</button>                          
                            <button type="submit" class="btn btn-primary" formaction="objects.php">View Objects</button>                                           
                            <button type="submit" class="btn btn-success" name="uuid" value="<?php echo $value;?>" formaction="../engine/Download.php">Download Target</button>
                        </form>
                    </div>
                </div>    
                <div class="col-md-4">
                    <form method="GET">   
                        <div class="col-md-2"><br>
                            <button type="submit" class="btn btn-success btn-lg glyphicon glyphicon-signal mybutton" name="value" value="ltm" formaction="modules.php"></button><br><br><br><BR><br><BR>
                        <button type="submit" class="btn btn-warning btn-lg glyphicon glyphicon-flag mybutton" name="value" value="gtm" formaction="modules.php"></button>
                        </div>
                        <div class="col-md-2">
                        <h3 class="nextto"><?php echo round($ltmp).'%';?></h3>                                                
                        <h3 class="tag"><small>LOADBALANCING CONVERSION</small></h3><br>
                        <h3 class="nextto"><?php echo $gslb?></h3>
                        <h3 class="tag"><small>GSLB</small></h3>
                        </div>
                        
                       
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="GET">
                        <div class="col-md-2"><br>
                            <button type="submit" class="btn btn-primary btn-lg glyphicon glyphicon-home mybutton"></button><br><br><br><br><br><BR>
                        <button type="submit" class="btn btn-warning btn-lg glyphicon glyphicon-flag mybutton" name="value" value="apm" formaction="modules.php"></button>
                        </div>
                        <div class="col-md-2">
                        <h3 class="nextto"><?php echo $objs;?></h3>
                        <h3 class="tag"><small>MODULES FOUND</small></h3><BR>
                        <h3 class="nextto"><?php echo $aaa;?></h3>
                        <h3 class="tag"><small>AAA</small></h3>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="GET">
                        <div class="col-md-2"><br>
                            <button type="submit" class="btn btn-danger btn-lg glyphicon glyphicon-flash mybutton" name="value" value="rule" formaction="modules.php"></button><br><br><br><br><br><BR>
                        <button type="submit" class="btn btn-warning btn-lg glyphicon glyphicon-flag mybutton" name="value" value="asm" formaction="modules.php"></button>
                        </div>
                        <div class="col-md-2">
                        <h3 class="nextto"><?php echo $rules;?></h3>
                        <h3 class="tag"><small>iRULES</small></h3><br><BR>
                        <h3 class="nextto"><?php echo $acl;?></h3>
                        <h3 class="tag"><small>APPFIREWALL</small></h3>
                        </div>
                    </form>
                    <br>
                </div>
                
                <table class="table">
                         <tr class="active">
                            <th class="text-center" style="width: 10%">F5 Module</th>
                            <th class="text-center" style="width: 15%">NetScaler Module</th> 
                            <th class="text-center" style="width: 13%">Total % of Config</th>                                                       
                            <th class="text-center" style="width: 14%">Converted</th>
                            <th class="text-center" style="width: 15%">Not Converted</th>
                            <th class="text-center" style="width: 13%">Omitted Lines</th>
                            <th class="text-center" style="width: 20%">Actions</th> 
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
                                    $total_F = $info[1] + $info[2] + $info[3];
                                    $t = ($total_F*100)/$total;
                                    $p_c = ($info[1]*100)/$total_data;
                                    $p_nc = ($info[2]*100)/$total_data;
                                    $p_o = ($info[3]*100)/$total_data;
                                    $omitted_p = $info[3];
                                    $module = $rows[$c][0];
                                    echo '<tr>';
                                    echo '<td>'.strtoupper($rows[$c][0]).'</td>';
                                    switch ($rows[$c][0]) {
                                        case 'ltm':
                                            $nsm = 'LOADBALANCING';
                                            break;
                                        case 'apm':
                                            $nsm = 'AAA';
                                            break;
                                        case 'gtm':
                                            $nsm = 'GSLB';
                                            break;
                                        case 'asm':
                                            $nsm = 'APPFIREWALL';
                                            break;
                                        default:
                                            $nsm='';
                                    }
                                    echo '<td>'.$nsm.'</td>';
                                    echo '<td class="text-center"><span class="badge badge_bkground_blue_sm">'.round($t).'%</span></td>';                                   
                                    echo '<td class="text-center"><span class="badge badge_bkground_green_sm">'.round($p_c).'%</span></td>';
                                    echo '<td class="text-center"><span class="badge badge_bkground_red_sm">'.round($p_nc).'%</span></td>';
                                    echo '<td class="text-center"><span class="badge badge_bkground_gray_sm">'.round($omitted_p).'</span></td>';
                                    echo '<td><a href="text.php?value='.$module.'#line">View Config</a>&nbsp;&nbsp;&nbsp;<a href="modules.php?value='.$module.'">View Module</a></td>';
                                    echo '</tr>';
                                    if ($rows[$c][0]=='ltm'){
                                        $info = $data->getCNCO($value, $rows[$c][0],'rule',0);
                                        $total_data = $info[1] + $info[2];
                                        $t = ($total_data*100)/$total;
                                        $p_c = ($info[1]*100)/$total_data;
                                        $p_nc = ($info[2]*100)/$total_data;
                                        $p_o = $info[3];                                       
                                        echo '<tr>';
                                        echo '<td>iRULE</td>';
                                        echo '<td>APPEXPERT</td>';
                                        echo '<td class="text-center"><span class="badge badge_bkground_blue_sm">'.round($t).'%</span></td>';
                                        
                                        echo '<td class="text-center"><span class="badge badge_bkground_green_sm">'.round($p_c).'%</span></td>';
                                        echo '<td class="text-center"><span class="badge badge_bkground_red_sm">'.round($p_nc).'%</span></td>';
                                        echo '<td class="text-center"><span class="badge badge_bkground_gray_sm">'.round($p_o).'</span></td>';
                                        echo '<td><a href="text.php?value=rule&line='.$tr[0]['line'].'#line">View Config</a>&nbsp;&nbsp;&nbsp;<a href="modules.php?value=rule">View Module</a></td>';
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