<?php

/**
 * REST API METHOD
 * 
 * Uploads a file, executes it to creat conversion, create stats for the
 * conversion, and link it up. This is a POST meathod that accepts a JSON 
 * input with an 'id' which is a string uuid.
 * 
 * @author  Andres Cubas <andresmcubas@gmail.com> 
 */

require_once dirname(__FILE__) .'/../model/AuthJwt.php';
require_once dirname(__FILE__) .'/../model/FileManager.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/UUID.php';
require_once dirname(__FILE__) .'/../model/TimeManager.php';
require_once dirname(__FILE__) .'/../model/Event.php';
require_once dirname(__FILE__) .'/../model/Conversions.php';
require_once dirname(__FILE__) .'/../model/Crud.php';
require_once dirname(__FILE__) .'/Config.php';

$jwt = AuthJwt::getTokenFromHeaders(getallheaders());
$auth = new AuthJwt();

if($auth->validJwt($jwt)){
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers');

    $users_id = $auth->getUserId($jwt);
    $projectid = null;

    $prog_conn = new CRUD();
    $prog_conn->update = "files";

    /* Start upload procedure */
    $c = new Config();

    $user = new User(['id' => $users_id]);
    $user->load();

    if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
        echo json_encode(["message" => "You must have a role of Engineer or Sales to use this functionality."]);
        exit();
    }

    $ct = explode(';', $_SERVER["CONTENT_TYPE"]);
    if($ct[0] != "multipart/form-data") {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode([
            "message" => "content-type must be multipart/form-data", 
            "received" => $_SERVER["CONTENT_TYPE"]
        ]);
        return;
    }
    $uuid = new UUID(); //get UUID
    $uuid = $uuid->v4();
    $file = new FileManager(array( 
        '_path_files' => $c->path_files(), 
        'uuid' => $uuid));

    echo json_encode(['id' => $uuid]);

    try {

        if($_SERVER['CONTENT_LENGTH'] > 8388608) 
            throw new Exception("File exceeds size of 8M. Please try another file");
        $file_name = $_FILES["file"]["name"];
        $file->CheckFile(); 
        $so = $file->_message;
        if($so != false) throw new Exception("File already exists. Please try another file");
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
            throw new Exception("Cannot process this type of file. Sorry.<br>File \"$file_name\" is of type $asciibin.");
        }
        $file->uuid = $uuid;
        $file->filename = $file_name;
        $file->upload_time = $date;
        $file->users_id = $users_id;
        $file->size = $_FILES['file']['size'];

        $file->save();
        new Event($user, "File \"$file_name\" was uploaded succesfully.", 6);

        $prog_conn->set = "conv_progress='Uploaded.'";
        $prog_conn->condition = "uuid='$uuid'";
        $prog_conn->Update();
    } catch (Exception $ex) {
        new Event($user, $ex->getMessage());
        $prog_conn->set = "conv_progress='" . $ex->getMessage() . "'";
        $prog_conn->condition = "uuid='$uuid'";
        $prog_conn->Update();
        die();
    }


    /* Start execute procedure */

    // $process = array();
    try {
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
        $prog_conn->set = "conv_progress='Conversion finished.'";
        $prog_conn->Update();
        new Event($user, "Conversion of \"$file_name\" Finished.", 7);
    } catch (Exception $ex) {
        new Event($user, $ex->getMessage());
        $prog_conn->set = "conv_progress='" . $ex->getMessage() . "'";
        $prog_conn->Update2();
        die();
    }

    /* Start stats procedure */

    try {
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

        $prog_conn->set = "conv_progress='Statistics Generated.'";
        $prog_conn->Update();
        new Event($user, "Statistics generated for \"$file_name\"", 8);
    } catch (Exception $ex) {
        error_log($ex->getMessage());
        $prog_conn->set = "conv_progress='" . $ex->getMessage() . "'";
        $prog_conn->Update();
        new Event($user, $ex->getMessage());
    }

    /* Start links procedure */

    try {
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

        $prog_conn->set = "conv_progress='Links Generated. Process ended.'";
        $prog_conn->Update();
        new Event($user, "Links generated for \"$file_name\"", 8);
    } catch (Exception $ex) {
        error_log($ex->getMessage());
        $prog_conn->set = "conv_progress='" . $ex->getMessage() . "'";
        $prog_conn->Update();
        new Event($user, $ex->getMessage());
    }
} else{
    header("HTTP/1.1 401 Unauthorized");
}
