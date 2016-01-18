<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require '../model/StartSession.php';
require '../model/TimeManager.php';
require '../model/Crud.php';
require '../model/ConnectionBD.php';
require '../model/Netpivot.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario');
$id = $sesion->get('id');
$user_type = $sesion->get('type'); 

$uuid = htmlspecialchars($_GET['uuid']);
$filename = htmlspecialchars($_GET['filename']);
$c_file = $uuid.'_.conf';
$path_pivot = '/opt/netpivot/';
$path_files = '/var/www/html/dashboard/files/';

$error_name = $uuid . '_error.txt';
// $stats_name = $uuid . '_stats.txt';
$csv_name = $uuid . '_stats.csv';
$command = $path_pivot. 'f5conv -f '.$path_files . $uuid . ' -e ' .$path_files. $error_name . ' -C '. $path_files.$csv_name . ' -O ' .$path_files. $c_file;
$csv_file = 'files/'. $csv_name;

try {
    $pwd = exec($command, $pwd_out,$pwd_error); //this is the command executed on the host  
    $time = new TimeManager();
    $time->Today_Date();
    $today = $time->full_date;                        
    $model = new Crud();
    $model->insertInto = 'conversions';
    $model->insertColumns = 'users_id,time_conversion,files_uuid,converted_file,error_file,stats_file';
    $model->insertValues = "'$id','$today','$uuid','$c_file','$error_name','$csv_name'";
    $model->Create();
    $msg = $model->mensaje;
    if ($msg == true) {

        $modelo = new Crud();
        if (($handle = fopen($csv_file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $total = count($data);
                $modelo->insertInto='details';
                $modelo->insertColumns='files_uuid,module,obj_grp,obj_component,obj_name,attribute,converted,omitted,line';
                $string ="'".$uuid."',";
                for ($c=0;$c<$total;$c++){
                    if ($c != $total-1){
                        $string= $string . "'". $data[$c] ."',";
                    } else {
                        $string= $string . "'". $data[$c] ."'";
                    }
                }
                $modelo->insertValues = $string;
                $modelo->Create();
                $msg2 = $modelo->mensaje;   
            }
        }
        if ($msg2 == true){
            $sesion->set('uuid', $uuid);
            header ('location:brief.php');
        } else {
            echo 'error aqui';
        }

    }
    else {
        header ('location:command.php?error');
    }
} catch (Exception $ex) {
    header ('location:command.php?fatal');
}

