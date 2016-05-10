
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
$npmodules = $sesion->get('npmodules');

if($usuario == false ) { 
    header('location: /'); 
    exit();
}
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

        $npo = new Crud();
        $npo->select='*';
        if($module != 'rule') {
            $npo->from='obj_grps_view';
            $npo->condition='module_id='.$npmodules[$module]["id"];
        } else {
            $npo->from='obj_names_view';
            $npo->condition='obj_grp_id='.$npmodules[$module]["id"];
        }
        $npo->Read();

        foreach ($npo->rows as $v) {
            $npobjgrp[$v["name"]] = $v;
            if($module == 'rule') {
                $npobjgrp[$v["name"]]["object_count"] = $npobjgrp[$v["name"]]["attribute_count"];
            }
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
               <?php include('../engine/breadcrumbs.php');//Includethe breadcrumb ?>
            </div>
            <div class="panel-body"> 
                <div class="row">
                    <div class="col-xs-6 content">
                        <h2 class="filename">Module Details</h2>
                    </div>
                    <div class="col-xs-6">
                        <form method="GET">
                            <button type="submit" class="btn btn-primary" formaction="brief.php">Summary</button>
                            <button type="submit" class="btn btn-primary" formaction="modules.php">Module Details</button>
                            <button type="submit" class="btn btn-primary" formaction="objects.php">Objects</button>
                            <button type="submit" class="btn btn-success" name="uuid" value="<?php echo $value;?>" formaction="../engine/Download.php">Download Target</button>
                        </form><br>
                    </div>
                </div> 
                <div class="col-md-12"><br><br>
                    <ul class="nav nav-pills">
                        <?php
                        foreach($npmodules as $mname => $m) {
                            if($mname == $module)
                                echo '<li role="presentation" class="active">';
                            else 
                                echo '<li role="presentation">';
                            echo '<a href="modules.php?value='. $mname. '">'. strtoupper($m["friendly_name"]). '</a></li>';
                        }
                        ?>
                    </ul>
                </div>
                    
                    <div class="col-lg-12">
                        <br>
                        <table  class="table" style="table-layout: fixed; width: 100%">
                             <tr class="active">
                                <th style="width: 30%">F5 Object Groups</th>
                                <th style="width: 10%"># Objects Found</th>
                                <th style="width: 15%">% Converted</th>
                                <th style="width: 15%">% Not Converted</th>
                                <th style="width: 10%"># Omitted</th>
                                <th style="width: 25%">Actions</th>
                            </tr>
                            <tbody>
                                <?php
                                    foreach ($npobjgrp as $ogname => $ogvalues) {
                                        if($ogname == 'rule') continue;
                                        if(isset($ogvalues["attribute_count"]) && $ogvalues["attribute_count"] > 0) {
                                            $p_c  = round(100 * $ogvalues["attribute_converted"] / $ogvalues["attribute_count"]);
                                            $p_nc = 100 - $p_c;
                                        } else {
                                            $p_c  = "-";
                                            $p_nc = "-";
                                        }
                                        echo '<tr>';
                                        echo '<td><div style="word-wrap: break-word">'.$ogvalues["name"].'</div></td>';
                                        echo '<td>'.$ogvalues["object_count"]."</td>";
                                        echo '<td class="text_color_green"><strong>'.$p_c."%</strong></td>";
                                        echo '<td class="text_color_red"><strong>'.$p_nc."%</strong></td>";
                                        echo '<td class="text_color_gray"><strong>'.$ogvalues["attribute_omitted"]."</strong></td>";
                                        echo '<td><a href="objects.php?value='.$module.'&obj='.$ogname.'">View Object</a>';
                                        echo '<tr>';
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
