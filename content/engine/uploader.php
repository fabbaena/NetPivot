<?php

require_once '../model/StartSession.php';
require_once '../model/UserList.php';
require_once '../model/FileManager.php';
require_once '../model/UUID.php';
require_once '../model/TimeManager.php';
require_once '../model/Event.php';
require_once 'Config.php';

$session = new StartSession();
$user = $session->get('user');

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: ../');
    exit();
}

$id= $user->id;

$c = new Config();


$uuid = new UUID(); //get UUID
$uuid = $uuid->v4();
$file = new FileManager(array( 
    '_path_files' => $c->path_files(), 
    'uuid' => $uuid));

$process = array(
    'result' => 'error',
    'message' => 'Unknown error.',
    'next' => 'none'
    );

try {
    if($_SERVER['CONTENT_LENGTH'] > 8388608) 
        throw new Exception("File exceeds size of 8M. Please try another file");
    $file_name = $_FILES['InputFile']['name'];
    $file->CheckFile(); 
    $so = $file->_message;
    if($so != false) throw new Exception("File already exists. Please try another file");
    $session->set('uuid', $uuid);
    $c->set_uuid($uuid);
    syslog(LOG_INFO, "Uploaded file ". $_FILES['InputFile']['tmp_name']. " to ". $c->f5_file());
    if(!move_uploaded_file($_FILES['InputFile']['tmp_name'], $c->f5_file())) 
        throw new Exception("Unable to move file. Internal Error. Please contact the administrator. (". 
            $_FILES['InputFile']['tmp_name']. ")");

    $time = new TimeManager(); //get Date
    $time->Today_Date();
    $date = $time->full_date;
    
    $asciibin = exec($c->file_type());
    if(strpos($asciibin, "ASCII text") === false) {
        unlink($c->f5_file());
        $session->delete("uuid");
        throw new Exception("Cannot process this type of file. Sorry.<br>File \"$file_name\" is of type $asciibin.");
    }
    $file->uuid = $uuid;
    $file->filename = $file_name;
    $file->upload_time = $date;
    $file->users_id = $id;
    $file->size = $_FILES['InputFile']['size'];

    $file->save();
    $progress["result"] = "Done";
    $progress["message"] = "Uploaded";
    $progress["next"] = "convert";
    $progress["uuid"] = $uuid;
    $session->set('upload_file_name', $file_name);
    new Event($user, "File \"$file_name\" was uploaded succesfully.", 6);
} catch (Exception $ex) {
    new Event($user, $ex->getMessage());
    $progress["message"] = $ex->getMessage();
    $progress["result"] = "Error";
}
echo json_encode($progress);
?>