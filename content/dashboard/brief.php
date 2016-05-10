<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../model/StartSession.php';
require '../model/Crud.php';
require '../model/ConnectionBD.php';
 
$sesion = new StartSession();
$usuario = $sesion->get('usuario'); //Get username
$id= $sesion->get('id'); //Get user id
$user_type = $sesion->get('type'); //Get user type = administrator or user
$max_files = $sesion->get('max_files');
$filename  = $sesion->get('filename');
if (isset($_POST['uuid'])) {
     $uuid = htmlspecialchars($_POST['uuid']);
     $sesion->set('uuid', $uuid);
} else {
    $uuid = $sesion->get('uuid');
}

if(!isset($filename) || $filename == "" ) {
    $info = new Crud();
    $info->select ='filename';
    $info->from='files';
    $info->condition='uuid="'.$uuid.'"';
    $info->Read();
    $filename = $info->rows[0]["filename"];
    $sesion->set('filename', $filename);
}

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
                
        $npm = new Crud();
        $npm->select='*';
        $npm->from='modules_view';
        $npm->condition='files_uuid="'.$uuid.'"';
        $npm->Read();

        $totalattributes = 0;
        $ltmid="";
        foreach ($npm->rows as $value) {
            $npmodules[$value["name"]]["files_uuid"]          = $value["files_uuid"];
            $npmodules[$value["name"]]["id"]                  = $value["id"];
            $npmodules[$value["name"]]["friendly_name"]       = $value["name"];
            $npmodules[$value["name"]]["objgrp_count"]        = $value["objgrp_count"];
            $npmodules[$value["name"]]["object_count"]        = $value["object_count"];
            $npmodules[$value["name"]]["attribute_count"]     = $value["attribute_count"];
            $npmodules[$value["name"]]["attribute_converted"] = $value["attribute_converted"];
            $npmodules[$value["name"]]["attribute_omitted"]   = $value["attribute_omitted"];
            $totalattributes += $value["attribute_count"];
            switch ($value["name"]) {
                case 'ltm':
                    $npmodules[$value["name"]]["ns_name"] = 'LOADBALANCING';
                    break;
                case 'apm':
                    $npmodules[$value["name"]]["ns_name"] = 'AAA';
                    break;
                case 'gtm':
                    $npmodules[$value["name"]]["ns_name"] = 'GSLB';
                    break;
                case 'asm':
                    $npmodules[$value["name"]]["ns_name"] = 'APPFIREWALL';
                    break;
                default:
                    $npmodules[$value["name"]]["ns_name"] = '';
            }
            if($value["name"] == "ltm") {
                $ltmid = $value["id"];
                $npo = new Crud();
                $npo->select='*';
                $npo->from='obj_grps_view';
                $npo->condition='module_id='.$ltmid.' AND name="rule"';
                $npo->Read();
                $npmodules["rule"]["friendly_name"]       = "iRULE";
                $npmodules["rule"]["id"]                  = $npo->rows[0]["id"];
                $npmodules["rule"]["object_count"]        = $npo->rows[0]["object_count"];
                $npmodules["rule"]["attribute_count"]     = $npo->rows[0]["attribute_count"];
                $npmodules["rule"]["attribute_converted"] = $npo->rows[0]["attribute_converted"];
                $npmodules["rule"]["attribute_omitted"]   = $npo->rows[0]["attribute_omitted"];
                $npmodules["rule"]["ns_name"]             = "APPEXPERT";

                $npmodules["ltm"]["attribute_count"]     -= $npmodules["rule"]["attribute_count"];
                $npmodules["ltm"]["attribute_converted"] -= $npmodules["rule"]["attribute_converted"];
                $npmodules["ltm"]["attribute_omitted"]   -= $npmodules["rule"]["attribute_omitted"];
            }

        }
        $sesion->set('npmodules', $npmodules);

        $ltmconverted = 100 * $npmodules["ltm"]["attribute_converted"] / $npmodules["ltm"]["attribute_count"];
        $gslbcount  = isset($npmodules["gtm"])?$npmodules["gtm"]["object_count"]:0;
        $aaacount   = isset($npmodules["apm"])?$npmodules["apm"]["object_count"]:0;
        $appfwcount = isset($npmodules["asm"])?$npmodules["asm"]["object_count"]:0;
        $irulecount = $npmodules["rule"] ["object_count"]?$npmodules["rule"]["object_count"]:0;


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
                        <form method="GET">
                            <button type="submit" class="btn btn-primary" formaction="brief.php">Summary</button>
                            <button type="submit" class="btn btn-primary" formaction="modules.php">Module Details</button>
                            <button type="submit" class="btn btn-primary" formaction="objects.php">Objects</button>
                            <button type="submit" class="btn btn-success" name="uuid" value="<?php echo $uuid;?>" formaction="../engine/Download.php">Download Target</button>
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
                        <h3 class="nextto"><?php echo round($ltmconverted).'%';?></h3>                                                
                        <h3 class="tag"><small>LOADBALANCING CONVERSION</small></h3><br>
                        <h3 class="nextto"><?php echo $gslbcount ?></h3>
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
                        <h3 class="nextto"><?php echo count($npmodules);?></h3>
                        <h3 class="tag"><small>MODULES FOUND</small></h3><BR>
                        <h3 class="nextto"><?php echo $aaacount ;?></h3>
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
                        <h3 class="nextto"><?php echo $irulecount;?></h3>
                        <h3 class="tag"><small>iRULES</small></h3><br><BR>
                        <h3 class="nextto"><?php echo $appfwcount;?></h3>
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
                                foreach ($npmodules as $modulename => $value) {
                                    echo '<tr>';
                                    echo '<td>'.strtoupper($value["friendly_name"]).'</td>';
                                    if(isset($value["attribute_count"])) {
                                        $p_t  = 100 * $value["attribute_count"]     / $totalattributes;
                                        if(isset($value["attribute_count"]) && $value["attribute_count"] > 0) {
                                            $p_c  = 100 * $value["attribute_converted"] / $value["attribute_count"];
                                        } else {
                                            $p_c = 0;
                                        }
                                        $p_nc = 100 - $p_c;
                                        $p_t  = $p_t<1?($p_t==0?"0":"<1"):round($p_t);
                                        $p_c  = $p_c<1?($p_c==0?"0":"<1"):round($p_c);
                                        $p_nc = $p_nc<1?($p_nc==0?"0":"<1"):round($p_nc);
                                    } else {
                                        $p_t = '-';
                                        $p_c = '-';
                                        $p_nc = '-';
                                    }
                                    
                                    echo '<td>'.$value["ns_name"].'</td>';
                                    echo '<td class="text-center"><span class="badge badge_bkground_blue_sm">'.$p_t.'%</span></td>';
                                    echo '<td class="text-center"><span class="badge badge_bkground_green_sm">'.$p_c.'%</span></td>';
                                    echo '<td class="text-center"><span class="badge badge_bkground_red_sm">'.$p_nc.'%</span></td>';
                                    echo '<td class="text-center"><span class="badge badge_bkground_gray_sm">'.$value["attribute_omitted"].'</span></td>';
                                    echo '<td><a href="modules.php?value='.$modulename.'">View Module</a></td>';
                                    echo '</tr>';
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
