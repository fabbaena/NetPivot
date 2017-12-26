<?php

require_once '../model/StartSession.php';
require_once '../model/UserList.php';
require_once '../model/UUID.php';
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

$process = array(
    'result' => 'error',
    'message' => 'Unknown error.',
    'next' => 'none'
    );

try {
    $path_files = '/var/www/nginx_files/'. $uuid;

    if($_SERVER['CONTENT_LENGTH'] > 8388608) 
        throw new Exception("File exceeds size of 8M. Please try another file");
    $file_name = $_FILES['InputFile']['name'];
    if (file_exists($path_files))
        throw new Exception("File already exists. Please try another file");
    syslog(LOG_INFO, "Uploaded file ". $_FILES['InputFile']['tmp_name']. " to ". $path_files);
    if(!move_uploaded_file($_FILES['InputFile']['tmp_name'], $path_files)) 
        throw new Exception("Unable to move file. Internal Error. Please contact the administrator. (". 
            $_FILES['InputFile']['tmp_name']. ")");
    
    $asciibin = exec('/usr/bin/file '. $path_files);

    if(strpos($asciibin, "ASCII text") === false && strpos($asciibin, "Unicode text") === false) {
        unlink($path_files);
        throw new Exception("Cannot process this type of file. Sorry.<br>File \"$file_name\" is of type $asciibin.");
    }
    $progress["result"] = "Done";
    $progress["message"] = "Uploaded";
    $progress["next"] = "convert";
    $progress["uuid"] = $uuid;
    new Event($user, "Nginx Plus File \"$file_name\" was uploaded succesfully.", 6);
    $session->set('upload_file_name', $file_name);
} catch (Exception $ex) {
    new Event($user, $ex->getMessage());
    $progress["message"] = $ex->getMessage();
    $progress["result"] = "Error";
}
echo json_encode($progress);
?>
