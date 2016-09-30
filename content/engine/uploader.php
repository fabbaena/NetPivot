<?php

require_once '../model/StartSession.php';
require_once '../model/UserList.php';
require_once '../model/FileManager.php';
require_once '../model/UUID.php';
require_once '../model/TimeManager.php';
require_once 'Config.php';

$session = new StartSession();
$user = $session->get('user');

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: ../');
    exit();
}

$id= $user->id;

$c = new Config();

$file_name = $_FILES['InputFile']['name'];

$uuid = new UUID(); //get UUID
$uuid = $uuid->v4();
$file = new FileManager(array( 
    '_path_files' => $c->path_files(), 
    'uuid' => $uuid));
$file->CheckFile(); 
$so = $file->_message;

if ($so==false) {
    $session->set('uuid', $uuid);
    $c->set_uuid($uuid);
    syslog(LOG_INFO, "Uploaded file ". $c->f5_file());
    if(move_uploaded_file($_FILES['InputFile']['tmp_name'], $c->f5_file())) {
        try {
            $time = new TimeManager(); //get Date
            $time->Today_Date();
            $date = $time->full_date;
            
            $asciibin = exec($c->file_type());
            if(strpos($asciibin, "ASCII text") === false) {
                unlink($c->f5_file());
                $session->delete("uuid");
                header("location: ../dashboard/?e=1");
                exit(0);
            }
            $file->uuid = $uuid;
            $file->filename = $file_name;
            $file->upload_time = $date;
            $file->users_id = $id;

            $file->save();

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