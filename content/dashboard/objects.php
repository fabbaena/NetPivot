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
        if (isset($_GET['obj'])){
            $obj = $_GET['obj'];
        } else {
            $obj= '';
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
                        <h2 class="filename">Object Details</h2>
                    </div>
                    <div class="col-md-6">
                        <form method="POST">      
                            <button type="submit" class="btn btn-primary" formaction="brief.php">View Summary</button>                          
                            <button type="submit" class="btn btn-primary" formaction="modules.php">View Module Details</button>                                           
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
                            $z->condition='files_uuid="'.$value.'" AND module="ltm" AND obj_grp="rule"';
                            $z->Read();
                            $y= $z->rows;
                        
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
                                    echo '<button type="submit" class="btn btn-default" name="value" value="'.$b[$c][0].'" formaction="objects.php">'.  strtoupper($b[$c][0]).'</button>';
                                } elseif ($b[$c][0]=='ltm' AND $module==$b[$c][0] AND $y!=null) {    
                                    echo '<button type="submit" class="btn btn-default active">'.  strtoupper($b[$c][0]).'</button>';
                                    echo '<button type="submit" class="btn btn-default" name="value" value="rule" formaction="objects.php">iRULES</button>';
                                } elseif ($b[$c][0]=='ltm' AND $module!=$b[$c][0] AND $module!='rule'){
                                  
                                    echo '<button type="submit" class="btn btn-default" name="value" value="ltm" formaction="objects.php">LTM</button>';
                                    echo '<button type="submit" class="btn btn-default" name="value" value="rule" formaction="objects.php">iRULES</button>';
                                } elseif ($b[$c][0]=='ltm' AND $module!=$b[$c][0] AND $module=='rule') {
                                    echo '<button type="submit" class="btn btn-default" name="value" value="ltm" formaction="objects.php">LTM</button>';
                                    echo '<button type="submit" class="btn btn-default active" name="value" value="rule" formaction="objects.php">iRULES</button>';
                                }
                            }
                        ?>
                    </form>
                </div>
                
                    <div class="col-md-3 no-padding custom-margin-top">
                        <div class="side-menu">
                            <nav class="navbar navbar-default no-black" role="navigation">
                                <!-- Main Menu -->
                                <div class="side-menu-container">
                                    
                                    <ul class="nav nav-pills nav-stacked custom-side-menu">
                                        <li class="list-group-item text-center gray_backgr"><strong>Object Group Name</strong></li>
                                        <?php
                                            
                                            $model = new Crud();
                                            $model->select='DISTINCT (obj_grp)';
                                            $model->from='details';
                                            $model->condition='files_uuid="'.$value.'" AND module="'.$module.'"';
                                            $model->Read();
                                            $rows = $model->rows;
                                            $total2 = count ($rows);
                                            if ($obj=='') {
                                                $obj = $rows[0][0];
                                            }
                                            for ($c=0;$c<$total2;$c++){
                                                if ($rows[$c][0] !='rule') {
                                                    if ($rows[$c][0]== $obj){
                                                        echo '<li class="active"><a href="objects.php?value='.$module.'&obj='.$rows[$c][0].'">'.$rows[$c][0].'</a></li>';                                             
                                                    } else {
                                                         echo '<li><a href="objects.php?value='.$module.'&obj='.$rows[$c][0].'">'.$rows[$c][0].'</a></li>';
                                                    }
                                                }    
                                            }
                                            if ($module == 'rule') { //the side menu for irules button                                                
                                                $t = new Crud();
                                                $t->select='DISTINCT (obj_name)';
                                                $t->from='details';
                                                $t->condition='files_uuid="'.$value.'" AND module="ltm" AND obj_grp="rule"';
                                                $t->Read();
                                                $s = $t->rows;
                                                $total = count($s);
                                                if ($obj=='') {
                                                    $obj = $s[0][0];
                                                }
                                                for ($k=0;$k<$total;$k++) {
                                                    $gf = str_replace('/Common/', '', $s[$k][0]);
                                                    $tk = wordwrap($gf,30,"<br>",true);
                                                    
                                                    if ($s[$k][0]== $obj){
                                                        echo '<li class="active"><a href="objects.php?value='.$module.'&obj='.$rows[$k][0].'">'.$tk.'</a></li>';                                             
                                                    } else {
                                                         echo '<li><a href="objects.php?value='.$module.'&obj='.$s[$k][0].'">'.$tk.'</a></li>';
                                                    }
                                                }
                                            }
                                        ?>
                                       
                                    </ul>
                                </div><!-- /.navbar-collapse -->
                            </nav>
                        </div>   
                    </div>
                    <div class="col-lg-9 no-padding">
                        <br>
                        <table class="table" style="table-layout: fixed; width: 100%">
                             
                                <?php
                                
                                    if ($module=='rule' ) { //exception for the irule button
                                        echo '<tr class="active">                          
                                            <th style="width: 15%">Comment</th>
                                            <th style="width: 10%">Line #</th>
                                            <th style="width: 15%">Actions</th>
                                            </tr>
                                            <tbody>';
                                            $h = new Crud();
                                            $h->select='*';
                                            $h->from='details';
                                            $h->condition='files_uuid="'.$value.'" AND module="ltm" AND obj_grp="rule" AND obj_name="'.$obj.'"';
                                            $h->Read();
                                            $v = $h->rows;                                            
                                            $w = count($v);
                                            echo '<tr>';
                                            for ($d=0;$d<$w;$d++){
                                                echo '<td>';
                                                echo $v[$d]['attribute'];
                                                echo '</td>';
                                                echo '<td>'.$v[$d]['line'].'</td>';
                                                echo '<td><a href="text.php?value=rule&obj='.$obj.'&line='.$v[$d]['line'].'#line">View Config Text</a></td>'; 
                                                echo '</tr>';
                                            }
                                        
                                        
                                    } else {
                                        echo '<tr class="active">
                                            <th style="width: 25%">Object Name</th>
                                            <th style="width: 15%">% Converted</th>
                                            <th style="width: 20%">% Not Converted</th>
                                            <th style="width: 15%"># Omitted</th>
                                            <th style="width: 20%">Actions</th>
                                            </tr>
                                            <tbody>';
                                        $t = new NetPivot();                                        
                                        $r = $t->getCNCO_Obj($value, $module, $obj);
                                        $s = count($r);                                      
                                        for ($u=0;$u<$s;$u++){
                                            $hj = new Crud();
                                            $hj->select='line';
                                            $hj->from='details';
                                            $hj->condition='files_uuid="'.$value.'" AND module="'.$module.'" AND obj_grp="'.$obj.'" AND obj_name="'.$r[$u]['name'].'"';
                                            $hj->Read();
                                            $uline = $hj->rows;
                                            $linenum = $uline[0]['line'];
                                            
                                            $total = $r[$u]['converted'] + $r[$u]['no_converted'];
                                            $c = ($r[$u]['converted']*100)/$total;
                                            $nc = ($r[$u]['no_converted']*100)/$total;
                                            $o = ($r[$u]['omitted']*100)/$total;
                                            echo '<tr>';
                                            if ($r[$u]['name'] ==''){
                                                echo '<td><div style="word-wrap: break-word">'.$obj.'</div></td>';
                                            } else {
                                                echo '<td><div style="word-wrap: break-word">'.$r[$u]['name'].'</div></td>';
                                            }
                                            if ($c!=0) {
                                                echo '<td class="text_color_green"><strong>'.round($c).'%</strong></td>';
                                            } else {
                                                echo '<td class="text_color_gray"><strong>-</strong></td>';
                                            }
                                            if ($nc!=0) {
                                                echo '<td class="text_color_red"><strong>'.round($nc).'%</strong></td>';
                                            } else {
                                                echo '<td class="text_color_gray"><strong>-</strong></td>';
                                            }
                                            if ($r[$u]['omitted']!=0) {
                                                echo '<td class="text_color_gray"><strong>'.$r[$u]['omitted'].'</strong></td>';
                                            } else {
                                                echo '<td class="text_color_gray"><strong>-</strong></td>';
                                            }
                                            echo '<td><a href="text.php?value='.$module.'&obj='.$obj.'&line='.$linenum.'#line">View Config Text</a>';
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