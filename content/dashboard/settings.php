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
$usuario = $sesion->get('usuario'); //Got username
$id= $sesion->get('id'); //Got user id
$user_type = $sesion->get('type'); //Got user type = administrator or user

if ($user_type != 'Administrator') {
    header('location:../index.php');
}
        
if($usuario == false ) { 
    header('location: /'); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
    </head>    
    <?php include ('../engine/css.php');//Include links to stylesheets and js scripts ?>
        <title>NetPivot</title>  
    </head>
    <body >
    
    <?php include ('../engine/menu1.php');//Include the first part of the nav bar ?>
    
	
   
    <?php include ('../engine/menu2.php');//Include the second part of the nav bar?>	
        <div class="col-md-1"></div>
    <div class="col-md-10 content">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>NetPivot Settings</h4>
            </div>
            <div class="panel-body">                                    
                   <form enctype="multipart/form-data" action='../engine/save_settings.php' method="POST">                        
                        <div class="form-group">
                            <label for="Time_Zone">Set Time Zone: </label>
                            <select name="Time_Zone" id="Time_Zone">
                            <option value="" disabled="disabled" >Please select a name</option>
                                <?php                                
                                    $model = new Crud();
                                    $model->select = '*';
                                    $model->from = 'settings';
                                    //$model->condition = 'host_name=0';
                                    $model->Read();
                                    $data = $model->rows;
                                    foreach ($data as $is) {
                                        $timezone = $is['timezone'];
                                    }
                                    echo $timezone;
                                    $all = DateTimeZone::listIdentifiers();
                                    foreach ($all as $time) 
                                    {
                                        if ($time == $timezone) {
                                            echo '<option value="'.$time .'" selected="selected">'. $time.' </option>';
                                        } else {
                                            echo '<option value="'.$time .'">'. $time.' </option>';
                                        }                                   
                                    }
                                    echo '</select>';
                                ?>
                        </div>                                  
                        <input type="submit" value="Save">
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
