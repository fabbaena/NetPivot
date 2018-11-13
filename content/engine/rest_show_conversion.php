<?php

/**
 * REST API METHOD
 * 
 * Shows information about a particular conversion. The response contains
 * the conversion uuid, original file name, timestamp of when it was converted, 
 * and the conversion file size. This is a POST meathod that accepts a JSON 
 * input with an 'id' which is a string uuid.
 * 
 * @author  Andres Cubas <andresmcubas@gmail.com> 
 */

require_once dirname(__FILE__) .'/../model/AuthJwt.php';
require_once dirname(__FILE__) .'/../model/FileManager.php';
require_once dirname(__FILE__) .'/../model/Conversions.php';

$jwt = AuthJwt::getTokenFromHeaders(getallheaders());
$auth = new AuthJwt();

if($auth->validJwt($jwt)){

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Access-Control-Allow-Origin, Content-Type, Access-Control-Allow-Methods');

    $users_id = $auth->getUserId($jwt);

    // Obtain incoming request
    $incoming = json_decode(file_get_contents("php://input"));
    $uuid = $incoming->id;

    $fm = new FileManager(['users_id' => $users_id, 'uuid' => $uuid]);
    $conv = new Conversion(['users_id' => $users_id, 'files_uuid' => $uuid]);

    if($fm->load(['uuid', 'users_id']) and $conv->load(['files_uuid', 'users_id'])){

        $out = [
            'id' => $conv->files_uuid,
            'filename' => $fm->filename,
            'conversion-date' => $conv->conversion_time,
            'converted-size' => filesize($conv->converted_file)
        ];

        echo json_encode($out);
    } else{
        echo json_encode([
            'message' => 'No such conversion.'
        ]);
    }
} else{
    header("HTTP/1.1 401 Unauthorized");
}
