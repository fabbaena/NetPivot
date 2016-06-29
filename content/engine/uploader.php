<?php
require '../model/FileManager.php';
require '../model/Crud.php';
require '../model/UUID.php';
require '../model/TimeManager.php';
require '../model/StartSession.php';
require 'Config.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario');
$id= $sesion->get('id');
if($usuario == false ) { 
    header('location: /'); 
    exit();
}


$c = new Config();

$file_name = $_FILES['InputFile']['name'];

$file = new FileManager($c->path_files());
$file->file = $file_name;
$file->CheckFile(); 
$so = $file->message;

if ($so==false) {
        $uuid = new UUID(); //get UUID
        $value_uuid = $uuid->v4();
        $sesion->set('uuid', $value_uuid);
        $c->set_uuid($value_uuid);
        if(move_uploaded_file($_FILES['InputFile']['tmp_name'], $c->f5_file())) {
            try {
                $time = new TimeManager(); //get Date
                $time->Today_Date();
                $date = $time->full_date;
                
                $asciibin = exec($c->file_type());
                if(strpos($asciibin, "ASCII text") === false) {
                    unlink($c->f5_file());
                    $sesion->delete("uuid");
                    header("location: ../dashboard/?e=1");
                    exit(0);
                }
                $bt = exec($c->detect());
                if(strpos($bt, "BIGPIPE") !== false) {
                    $sesion->delete("uuid");
                    unlink($c->f5_file());
                    header("location: ../dashboard/?e=2");
                    exit(0);
                }
                if(strpos($bt, "UNKNOWN") !== false) {
                    $sesion->delete("uuid");
                    unlink($c->f5_file());
                    header("location: ../dashboard/?e=3");
                    exit(0);
                }


                $add = new Crud();
                $add->insertInto = 'files';
                $add->data = array(
                    "uuid"        => $value_uuid,
                    "filename"    => $file_name, 
                    "upload_time" => $date, 
                    "users_id"    => $id
                    );
                $add->Create2();
                header ('location:execute.php');
            } catch (Exception $ex) {
                    header ('location:../dashboard/index.php?upload_error');
            }
            
        } else{
            header ('location:../dashboard/index.php?upload_error');
        }
   } else {
            header ('location:../dashboard/index.php?exist_file='.$file_name.'');
    }
?>