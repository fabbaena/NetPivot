<?php

require_once '../model/FileManager.php';
require_once '../model/UserList.php';
require_once '../model/UUID.php';
require_once '../model/TimeManager.php';
require_once '../model/Event.php';
require_once '../model/Conversions.php';
require_once '../model/Crud.php';
require_once 'Config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, 
    Access-Control-Allow-Methods, Content-Type,
    Access-Control-Allow-Headers');

$users_id = 1;
$projectid = null;

$prog_conn = new CRUD();
$prog_conn->insertInto = 'rest_progress';
$prog_conn->update = 'rest_progress';

/* PUT data comes in on the stdin stream */
// $putdata = json_decode(file_get_contents("php://input"));

// echo $putdata->file;

/* Start upload procedure */
$c = new Config();

$user = new User(['id' => $users_id]);
$user->load();

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    echo json_encode(["message" => "You must have a role of Engineer or Sales to use this functionality."]);
    exit();
}

$uuid = new UUID(); //get UUID
$uuid = $uuid->v4();
$file = new FileManager(array( 
    '_path_files' => $c->path_files(), 
    'uuid' => $uuid));

echo json_encode(['uuid' => $uuid]);

$process = array(
    'uuid' => $uuid,
    'message' => 'Initializing process.',
    'next' => 'upload'
    );
$prog_conn->data = $process;

try {
    $prog_conn->Create2();

    if($_SERVER['CONTENT_LENGTH'] > 8388608) 
        throw new Exception("File exceeds size of 8M. Please try another file");
    // $file_name = $putdata->filename;
    $file_name = $_FILES["file"]["name"];
    $file->CheckFile(); 
    $so = $file->_message;
    if($so != false) throw new Exception("File already exists. Please try another file");
    // $session->set('uuid', $uuid);
    $c->set_uuid($uuid);
    syslog(LOG_INFO, "REST: Uploaded file ". $_FILES['file']['tmp_name']. " to ". $c->f5_file());
    if(!move_uploaded_file($_FILES['file']['tmp_name'], $c->f5_file())) 
        throw new Exception("Unable to move file. Internal Error. Please contact the administrator. (". 
        $_FILES['file']['tmp_name']. ")");

    $time = new TimeManager(); //get Date
    $time->Today_Date();
    $date = $time->full_date;
    
    $asciibin = exec($c->file_type());
    //if(strpos($asciibin, "ASCII text") === false) {
    if(strpos($asciibin, "ASCII text") === false && strpos($asciibin, "Unicode text") === false) {
        unlink($c->f5_file());
        // $session->delete("uuid");
        throw new Exception("Cannot process this type of file. Sorry.<br>File \"$file_name\" is of type $asciibin.");
    }
    $file->uuid = $uuid;
    $file->filename = $file_name;
    $file->upload_time = $date;
    $file->users_id = $users_id;
    $file->size = $_FILES['InputFile']['size'];

    $file->save();
    // $process["result"] = "Done";
    $process["message"] = "Uploaded";
    $process["next"] = "convert";
    $prog_conn->data = $process;
    $prog_conn->Update2();
    // $process["uuid"] = $uuid;
    // $session->set('upload_file_name', $file_name);
    new Event($user, "File \"$file_name\" was uploaded succesfully.", 6);
} catch (Exception $ex) {
    new Event($user, $ex->getMessage());
    $process["message"] = $ex->getMessage();
    // $process["result"] = "Error";
    // echo json_encode($process);
    $prog_conn->data = $process;
    $prog_conn->Update2();
    die();
}


/* Start execute procedure */

// $process = array();
try {
    // if(!isset($_GET['uuid'])) throw new Exception("Did not receive any file to convert.");
    // $uuid = $_GET['uuid'];
    // $projectid = get_int($_GET, 'projectid');

    // $c = new Config($uuid);
    $c->convert_orphan(true);

    $pwd = exec($c->command(), $pwd_out,$pwd_error); //this is the command executed on the host  
    if($pwd_error) throw new Exception(
        "There was an error with the conversion process of \"$file_name\". ".
        "Please contact the administrator with the following information ". $uuid);

    $time = new TimeManager();
    $time->Today_Date();
    $today = $time->full_date;
    $conversion = new Conversion(array(
        "users_id"        => $user->id,
        "conversion_time" => $today,
        "files_uuid"      => $uuid,
        "converted_file"  => $c->ns_file(),
        "error_file"      => $c->error_file(),
        "stats_file"      => $c->stats_file(),
        "json_file"       => $c->json_file(),
        "projectid"       => $projectid
        ));

    if (!$conversion->save()) {
        throw new Exception("Could not save \"$file_name\" conversion to database. ".
            "Please contact the administrator with the following information: ". $uuid);
    }
    // $process["result"] = "ok";
    $process["message"] = "Conversion finished.";
    $process["next"] = "stats";
    $prog_conn->data = $process;
    $prog_conn->Update2();
    // $process["uuid"] = $uuid;
    new Event($user, "Conversion of \"$file_name\" Finished.", 7);
} catch (Exception $ex) {
    new Event($user, $ex->getMessage());
    // $process["result"] = "error";
    $process["message"] = $ex->getMessage();
    $prog_conn->data = $process;
    $prog_conn->Update2();
    // $session->set('upload_file_name', null);
    // echo json_encode($process);
    die();
}

/* Start stats procedure */

try {
    // if(!isset($_GET['uuid'])) throw new Exception("Did not receive any file to generate stats.");
    // $uuid = $_GET['uuid'];

    // $conversion = new Conversion(array("files_uuid" => $uuid));
    // $conversion->load('files_uuid');

    // $c = new Config($uuid);
    $string = file_get_contents($c->json_file());
    if(strlen($string) < 5) throw new Exception("Internal Error. Stats couldn't be generated for \"$file_name\". ($uuid)");
    $json_a = json_decode($string, true);

    if(isset($json_a['file_info'])) {
        $file_info = $json_a['file_info'];
        unset($json_a['file_info']);
        if(isset($file_info['np_version'])) {
            $conversion->np_version = $file_info['np_version'];
        }
        if(isset($file_info['F5_version'])) {
            $conversion->f5_version = $file_info['F5_version'];
        }
        $conversion->saveVersion();
    }

    if(isset($json_a['OTHER'])) {
        unset($json_a['OTHER']);
    }

    if(count($json_a) < 2) throw new Exception("Internal Error. JSON file error for \"$file_name\". ($uuid)");
    
    if(!$conversion->loadJSON($json_a)) 
        throw new Exception("Internal Error. Cannot load data into memory for \"$file_name\". ($uuid)");
    if(!$conversion->saveData()) 
        throw new Exception("Internal Error. Cannot load JSON data into database for \"$file_name\". ($uuid)");

    // $process["result"] = "ok";
    $process["message"] = "Statistics Generated.";
    $process["next"] = "links";
    $prog_conn->data = $process;
    $prog_conn->Update2();
    // $process["uuid"] = $uuid;
    new Event($user, "Statistics generated for \"$file_name\"", 8);
} catch (Exception $ex) {
    error_log($ex->getMessage());
    // $process["result"] = "error";
    $process["message"] = $ex->getMessage();
    $prog_conn->data = $process;
    $prog_conn->Update2();
    new Event($user, $ex->getMessage());
    // echo json_encode($process);
}

/* Start links procedure */

try {
    // if(!isset($_GET['uuid'])) throw new Exception("Did not receive any file to generate links.");
    // $uuid = $_GET['uuid'];

    // $conversion = new Conversion(array("files_uuid" => $uuid));
    // $conversion->load('files_uuid');

    // $c = new Config($uuid);
    $string = file_get_contents($c->f5nslink_file());
    if(strlen($string) < 5) throw new Exception("Internal Error. Links couldn't be generated for \"$file_name\". ($uuid)");
    $link_array = explode("\n", $string);

    $conn = new Crud();
    $conn->insertInto = "f5nslink";
    $conn->CreateBulk(true, array("f5", "ns", "files_uuid"));

    foreach($link_array as $l) {
        if($l == "") continue;
        $link = explode(",", $l);
        array_push($link, $uuid);

        $conn->data = $link;
        if($conn->CreateBulk() !== true) throw new Exception("DB error loading links. Details: ". $conn->mensaje);
    }

    // $process["result"] = "ok";
    $process["message"] = "Links Generated.";
    $process["next"] = "DONE";
    $prog_conn->data = $process;
    $prog_conn->Update2();
    // $process["uuid"] = $uuid;
    new Event($user, "Links generated for \"$file_name\"", 8);
} catch (Exception $ex) {
    error_log($ex->getMessage());
    // $process["result"] = "error";
    $process["message"] = $ex->getMessage();
    $prog_conn->data = $process;
    $prog_conn->Update2();
    new Event($user, $ex->getMessage());
    // echo json_encode($process);
}