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
        if (isset ($_GET['value'])){
            $module= $_GET['value'];
        } else {
            $module='ltm';
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
               <div class="col-md-6 content">
                <h1 class="projectname"><?php echo $filename;?></h1>
                <br>
               <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>">&lt; Go back</a>
               </div>
               
               <div class="col-md-6 content">
                   <br><br>
               <!-- <button type="submit" class="btn btn-default btn-lg margin-set pull-right glyphicon glyphicon-edit" name="uuid" value="<?php //echo $value;?>" formaction="#"> Edit</button>-->
               </div> 
            </div>
            <hr class="divider">
            <div class="panel-body"> 
                <div class="col-md-12">
                    <div class="col-md-6 content">     
                        <h2 class="filename">Module Details</h2>
                    </div>
                    <div class="col-md-6">
                        <form method="POST">      
                            <button type="submit" class="btn btn-primary" formaction="brief.php">View Summary</button>                          
                            <button type="submit" class="btn btn-primary" formaction="objects.php">View Objects</button>                                           
                            <button type="submit" class="btn btn-success" name="uuid" value="<?php echo $value;?>" formaction="../engine/Download.php">Download Target</button>
                        </form><br>
                    </div>
                </div> 
                <div class="col-md-12"><br><br>
                    <form method="GET">
                        <?php 
                            $z = new Crud();
                            $z->select='*';
                            $z->from='details';
                            $z->condition='files_uuid="'.$value.'" AND module="ltm" AND obj_grp="rule" GROUP BY obj_name';
                            $z->Read();
                            $y= $z->rows;
                            $r = count($y);
                            
                        
                            $a = new Crud();
                            $a->select='DISTINCT (module)';
                            $a->from='details';
                            $a->condition='files_uuid="'.$value.'"';
                            $a->Read();
                            $b = $a->rows;
                            $m_found= count($b);
                            for ($c=0;$c<$m_found;$c++){
                                if ($b[$c][0]!='ltm' AND $module==$b[$c][0]){
                                   
                                    echo '<button type="submit" class="btn btn-default active">'.  strtoupper($b[$c][0]).'</button>';
                                } elseif ($b[$c][0]!='ltm' AND $module!=$b[$c][0]){
                                    
                                    echo '<button type="submit" class="btn btn-default" name="value" value="'.$b[$c][0].'" formaction="modules.php">'.  strtoupper($b[$c][0]).'</button>';
                                } elseif ($b[$c][0]=='ltm' AND $module==$b[$c][0] AND $y!=null) {    
                                    
                                    echo '<button type="submit" class="btn btn-default active">'.  strtoupper($b[$c][0]).'</button>';
                                    echo '<button type="submit" class="btn btn-default" name="value" value="rule" formaction="modules.php">iRULES</button>';
                                } elseif ($b[$c][0]=='ltm' AND $module!=$b[$c][0] AND $module!='rule'){
                                  
                                    echo '<button type="submit" class="btn btn-default" name="value" value="ltm" formaction="modules.php">LTM</button>';
                                    echo '<button type="submit" class="btn btn-default" name="value" value="rule" formaction="modules.php">iRULES</button>';
                                } elseif ($b[$c][0]=='ltm' AND $module!=$b[$c][0] AND $module=='rule') {
                                    echo '<button type="submit" class="btn btn-default" name="value" value="ltm" formaction="modules.php">LTM</button>';
                                    echo '<button type="submit" class="btn btn-default active" name="value" value="rule" formaction="modules.php">iRULES</button>';
                                }
                            }  
                        ?>
                    </form>
                </div>
                    
                    <div class="col-lg-12">
                        <br>
                        <table  class="table" style="table-layout: fixed; width: 100%">
                             <tr class="active">
                                <th style="width: 20%">F5 Object Groups</th>
                                <th style="width: 15%">% Converted</th>
                                <th style="width: 15%"># Converted</th>
                                <th style="width: 15%"># Not Converted</th>
                                <th style="width: 10%"># Omitted</th>
                                <th style="width: 25%">Actions</th>
                            </tr>
                            <tbody>
                                <?php
                                    if ($module == "rule"){
                                        $t = new NetPivot();
                                        $r = $t->getCNCO_Obj($value,'ltm','rule');
                                        $s = count($r);
                                        for ($u=0;$u<$s;$u++){
                                                
                                        $total = $r[$u]['converted'] + $r[$u]['no_converted'];
                                        $c = ($r[$u]['converted']*100)/$total;

                                        echo '<tr>';
                                        $gf = str_replace('/Common/', '', $r[$u]['name']);
                                        echo '<td><div style="word-wrap: break-word">'.$gf.'</div></td>';
                                        if ($c!=0) {
                                            echo '<td class="text_color_green"><strong>'.round($c).'%</strong></td>';
                                        } else {
                                            echo '<td class="text_color_gray"><strong>-</strong></td>';
                                        }
                                        if ($r[$u]['converted']!=0) {
                                            echo '<td class="text_color_green"><strong>'.$r[$u]['converted'].'</strong></td>';
                                        } else {
                                            echo '<td class="text_color_gray"><strong>-</strong></td>';
                                        }
                                        if ($r[$u]['no_converted']!=0) {
                                            echo '<td class="text_color_red"><strong>'.$r[$u]['no_converted'].'</strong></td>';
                                        } else {
                                            echo '<td class="text_color_gray"><strong>-</strong></td>';
                                        }
                                        if ($r[$u]['omitted']!=0) {
                                            echo '<td class="text_color_red"><strong>'.$r[$u]['omitted'].'</strong></td>';
                                        } else {
                                            echo '<td class="text_color_gray"><strong>-</strong></td>';
                                        }
                                            echo '<td><a href="text.php?value='.$module.'&obj='.$r[$u]['name'].'&line='.$r[$u]['line'].'#line">View Config Text</a>&nbsp;&nbsp;&nbsp;<a href="objects.php?value='.$module.'&obj='.$rows[$c][0].'">View Object</a>';
                                            echo '<tr>';
                                        }
                                    }
                                    $model = new Crud();
                                    $model->select='DISTINCT (obj_grp)';
                                    $model->from='details';
                                    $model->condition='files_uuid="'.$value.'" AND module="'.$module.'"';
                                    $model->Read();
                                    $rows = $model->rows;
                                    $total2 = count ($rows);
                                    for ($c=0;$c<$total2;$c++){
                                        if ($rows[$c][0]!='rule') {        
                                            $a = new NetPivot();
                                            $b = $a->getCNCO($value, $module,$rows[$c][0],0);
                                            $b_sum = $b[1] + $b[2];
                                            $b_c = ($b[1]*100)/$b_sum;

                                            echo '<tr>';
                                            if ($module == 'rule'){
                                                echo '<td>'.$rows[$c][0].'</td>';
                                            } else {
                                                echo '<td>'.$rows[$c][0].'</td>';
                                            }
                                            if ($b_c !=0){
                                                echo '<td class="text_color_green"><strong>'.round($b_c).'%</strong></td>';
                                            } else {
                                                echo '<td class="text_color_gray"><strong>-</strong></td>';
                                            }
                                            if ($b[1] !=0){
                                                echo '<td class="text_color_green"><strong>'.$b[1].'</strong></td>';
                                            } else {
                                                echo '<td class="text_color_gray"><strong>-</strong></td>';
                                            }
                                           if ($b[2] !=0){
                                                echo '<td class="text_color_red"><strong>'.$b[2].'</strong></td>';
                                            } else {
                                                echo '<td class="text_color_gray"><strong>-</strong></td>';
                                            }
                                            if ($b[3] !=0){
                                                echo '<td class="text_color_red"><strong>'.$b[3].'</strong></td>';
                                            } else {
                                                echo '<td class="text_color_gray"><strong>-</strong></td>';
                                            }

                                            echo '<td><a href="text.php?value='.$module.'&obj='.$rows[$c][0].'#line">View Config Text</a>&nbsp;&nbsp;&nbsp;<a href="objects.php?value='.$module.'&obj='.$rows[$c][0].'">View Object</a>';
                                            echo '</tr>'; 
                                        }
                                    }
                                ?>
                                
                            </tbody>
                        </table>
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