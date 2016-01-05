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
            $module=null;
        }
        if (isset($_GET['obj'])){
            $obj = $_GET['obj'];
        } else {
            $obj= '';
        }
        if (isset($_GET['file'])){
            $tfile = $_GET['file'];
        } else {
            $tfile= 'f5';
        }
        if (isset($_GET['line'])) {
            $prep = $_GET['line'];
        } else {
            $prep = null;
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
        $project =  new Crud();
        $project->select='project_name';
        $project->from='files';
        $project->condition='uuid="'.$value .'"';
        $project->Read();
        $project2 = $project->rows;
        foreach ($project2 as $project_info){
            $project_name = $project_info['project_name'];
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
                    <div class="col-md-4 content">     
                        <h2 class="filename">Configuration File</h2>
                    </div>
                    <div class="col-md-8">
                        <form method="POST">      
                            <button type="submit" class="btn btn-primary" formaction="brief.php">View Summary</button>                          
                            <button type="submit" class="btn btn-primary" formaction="modules.php">View Module Details</button>
                            <button type="submit" class="btn btn-primary" formaction="objects.php">View Objects</button> 
                            <button type="submit" class="btn btn-success" name="uuid" value="<?php echo $value;?>" formaction="../engine/Download.php">Download Target</button>
                        </form><br>
                    </div>
                </div>
                <div class="col-md-12"><br>
                    <nav class="navbar navbar-default">    
                        <ul class="nav navbar-nav">
                           
                            <?php
                            
                                if ($tfile=='f5') {
                                    echo '<li class="active"><a href="text.php?file=f5">Source F5<span class="sr-only">(current)</span></a></li>';
                                    echo '<li><a href="text.php?file=ns">Target NetScaler</a></li>';
                                } elseif ($tfile=='ns') {
                                    echo '<li><a href="text.php?file=f5">Source F5</a></li>
                                    <li class="active"><a href="text.php?file=ns">Target NetScaler<span class="sr-only">(current)</span></a></li>';
                                }
                            ?>
                            
                            
                        </ul>
                    </nav>
                    <br>
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
                            
                            
                            if ($tfile=='ns'){                               
                                echo '<button type="submit" class="btn btn-default active" name="file" value="ns" formaction="text.php">LoadBalancing</button>';
                            } else {                              
                               for ($c=0;$c<$m_found;$c++){
                                if ($b[$c][0]!='ltm' AND $module==$b[$c][0]){
                                    echo '<button type="submit" class="btn btn-default active" >'.  strtoupper($b[$c][0]).'</button>';
                                } elseif ($b[$c][0]!='ltm' AND $module!=$b[$c][0]){
                                    echo '<button type="submit" class="btn btn-default" name="value" value="'.$b[$c][0].'" formaction="text.php#line">'.  strtoupper($b[$c][0]).'</button>';
                                } elseif ($b[$c][0]=='ltm' AND $module==$b[$c][0] AND $y!=null) {    
                                    echo '<button type="submit" class="btn btn-default active">'.  strtoupper($b[$c][0]).'</button>';
                                    echo '<button type="submit" class="btn btn-default" name="value" value="rule" formaction="text.php#line">iRULES</button>';
                                } elseif ($b[$c][0]=='ltm' AND $module!=$b[$c][0] AND $module!='rule'){
                                  
                                    echo '<button type="submit" class="btn btn-default" name="value" value="ltm" formaction="text.php#line">LTM</button>';
                                    echo '<button type="submit" class="btn btn-default" name="value" value="rule" formaction="text.php#line">iRULES</button>';
                                } elseif ($b[$c][0]=='ltm' AND $module!=$b[$c][0] AND $module=='rule') {
                                    echo '<button type="submit" class="btn btn-default" name="value" value="ltm" formaction="text.php#line">LTM</button>';
                                    echo '<button type="submit" class="btn btn-default active" name="value" value="rule" formaction="text.php#line">iRULES</button>';
                                } elseif ($module == null) {
                                    echo 'Any module selected';
                                }
                                
                            } 
                            }
                            
                        ?>
                    </form>
                </div>
                
                    <div class="col-md-3 no-padding custom-margin-top">
                        <div class="side-menu">
                            <nav class="navbar navbar-default" role="navigation">
                                <!-- Main Menu -->
                                <div class="side-menu-container">
                                    
                                    <ul class="nav nav-pills nav-stacked custom-side-menu">
                                        <li class="list-group-item text-center gray_backgr"><strong>Object Group Name</strong></li>
                                        <?php
                                            if ($module == 'rule'){
                                                $model = new Crud();
                                                $model->select='*';
                                                $model->from='details';
                                                $model->condition='files_uuid="'.$value.'" AND module="ltm" AND obj_grp="rule" GROUP by obj_name';
                                                $model->Read();
                                                $t = $model->rows;
                                                $total_t = count($t);
                                                if ($prep == null) {
                                                    $prep = $t[0]['line'];
                                                }                                               
                                                for($g=0;$g<$total_t;$g++){
                                                    $gf = str_replace('/Common/', '', $t[$g]['obj_name']);
                                                    $tk = wordwrap($gf,30,"<br>",true);
                                                        if ($t[$g]['obj_name']== $obj){
                                                        echo '<li class="active"><a href="text.php?value='.$module.'&obj='.$t[$g]['obj_name'].'&line='.$t[$g]['line'].'#line">'.$tk.'</a></li>';                                             
                                                       } else {
                                                            echo '<li><a href="text.php?value='.$module.'&obj='.$t[$g]['obj_name'].'&line='.$t[$g]['line'].'#line">'.$tk.'</a></li>';
                                                       }
                                                       
                                                }
                                            } else {
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
                                                            echo '<li class="active"><a href="text.php?value='.$module.'&obj='.$rows[$c][0].'#line">'.$rows[$c][0].'</a></li>';                                             
                                                        } else {
                                                             echo '<li><a href="text.php?value='.$module.'&obj='.$rows[$c][0].'#line">'.$rows[$c][0].'</a></li>';
                                                        }
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
                        <div style="overflow:scroll; height:800px;">
                        <table class="table" style="table-layout: fixed; width: 100%">
                             <tr class="active">
                                <th style="width: 10%">Line</th>
                                <th style="width: 90%">Content</th>
                                
                            </tr>
                            <tbody>
                                <?php
                                if ($module !=null){                                        
                                        $t = new Crud();
                                        $t->select='*';
                                        $t->from='details';
                                        $t->condition='files_uuid="'.$value.'" AND module="'.$module.'" AND obj_grp="'.$obj.'" order by line ASC LIMIT 1';
                                        $t->Read();
                                        $r = $t->rows;
                                        if ($prep == null ) {
                                            $line = $r[0]['line'];                                        
                                        } else {
                                            $line = $prep;
                                        }
                                        
                                    }
                                    
                                if ($tfile=='ns') {
                                    $f = new Crud();
                                    $f->select='converted_file';
                                    $f->from='conversions';
                                    $f->condition='files_uuid="'.$value.'"';
                                    $f->Read();
                                    $g = $f->rows;                                    
                                    $fileopen = $g[0]['converted_file'];
                                } else {
                                    $fileopen = $value;
                                }
                                
                                if ( !($gestor = fopen("files/$fileopen", "r"))) {
                                        exit ('Unable to open the input file') ;
                                } else {
                                        $c = 1;
                                        while (!feof($gestor))
                                        {
                                            $myline = fgets($gestor);
                                            
                                            if ($c == $line) {
                                                echo '<tr class="active">';
                                                echo '<td><h2 id="line"></h2><strong>'.$c.'</strong></td>';
                                            } else {
                                                echo '<tr>';
                                                echo '<td><strong>'.$c.'</strong></td>';
                                            }
                                            echo '<td><div style="word-wrap: break-word">'.$myline.'</div></td>';
                                            echo '</tr>';
                                            $c++;
                                        }
                                        fclose($gestor);
                                }
                                
                                ?>
                                
                                
                            </tbody>
                        </table> </div>
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