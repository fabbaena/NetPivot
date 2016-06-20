<?php
require '../model/FileManager.php';
require '../model/Crud.php';
require '../model/UUID.php';
require '../model/TimeManager.php';
require '../model/StartSession.php';
require '../model/Netpivot.php';

$sesion = new StartSession();
$usuario = $sesion->get('usuario');
$id= $sesion->get('id');
if($usuario == false ) { 
    header('location: /'); 
    exit();
}

$target_path = "../dashboard/files/";

$file_name = $_FILES['InputFile']['name'];

$model = new FileManager;
$model->file = $file_name;
$model->CheckFile(); 
$so = $model->message;

if ($so==false) {
        $target_path = $target_path . basename( $_FILES['InputFile']['name']); 
        if(move_uploaded_file($_FILES['InputFile']['tmp_name'], $target_path)) {
            try {
                $uuid = new UUID(); //get UUID
                $value_uudi = $uuid->v4();
                $time = new TimeManager(); //get Date
                $time->Today_Date();
                $date = $time->full_date;
                $old_name = '../dashboard/files/'. $file_name ;
                $new_name = '../dashboard/files/'. $value_uudi;
 
                //rename($old_name, $new_name); // Rename de file with the UUID
                $prepare = new NetPivot();
                $value = $prepare->PrepareF5File($new_name, $old_name);
                
                if ($value == true ){                   
                    $add = new Crud();
                    $add->insertInto = 'files';
                    $add->data = array(
                        "uuid"        => $value_uudi,
                        "filename"    => $file_name, 
                        "upload_time" => $date, 
                        "users_id"    => $id
                        );
                    $add->Create2();
                    $mensaje = $add->mensaje;
                    if ($mensaje == true){
                        $delete = new FileManager();
                        $delete->file = $file_name;
                        $delete->DeleteFile();
                        header ('location:../dashboard/execute.php?uuid='. $value_uudi .'&filename='.$file_name);
                    }
                } else {
                    header ('location:../dashboard/index.php?error_preparing');
                }
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