<?php
require '../model/FileManager.php';
require '../model/Crud.php';
require '../model/UUID.php';
require '../model/TimeManager.php';
require '../model/StartSession.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario');
$id= $sesion->get('id');
if($usuario == false ) { 
    header('location: /'); 
    exit();
}

require 'Config.php';

$file_name = $_FILES['InputFile']['name'];

$model = new FileManager($path_files);
$model->file = $file_name;
$model->CheckFile(); 
$so = $model->message;

if ($so==false) {
        $uuid = new UUID(); //get UUID
        $value_uuid = $uuid->v4();
        $sesion->set('uuid', $value_uuid);
        $target_path = $path_files . $value_uuid; 
        if(move_uploaded_file($_FILES['InputFile']['tmp_name'], $target_path)) {
            try {
                $time = new TimeManager(); //get Date
                $time->Today_Date();
                $date = $time->full_date;
                
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