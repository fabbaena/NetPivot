<?php

/**
 * REST API METHOD
 * 
 * Respond with a generated stats file for the specified conversion.
 * This is a POST meathod that accepts a JSON input with an 'id' 
 * which is a string uuid.
 * 
 * @author  Andres Cubas <andresmcubas@gmail.com> 
 */

require_once dirname(__FILE__) .'/../model/AuthJwt.php';
require_once dirname(__FILE__) .'/../model/Conversions.php';

$jwt = AuthJwt::getTokenFromHeaders(getallheaders());
$auth = new AuthJwt();
if ($auth->validJwt($jwt)){
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,
        Access-Control-Allow-Origin, Content-Type,
        Access-Control-Allow-Methods');

    $users_id = $auth->getUserId($jwt);
    // Obtain incoming request
    $incoming = json_decode(file_get_contents("php://input"));
    $uuid = $incoming->id;

    $conv = new Conversion(['files_uuid' => $uuid, 'users_id' => $users_id]);

    if($conv->load(['files_uuid', 'users_id'])){

        readfile($conv->json_file);

    } else{
        echo json_encode([
            'message' => 'No such conversion.'
        ]);
    }

}else{
    header("HTTP/1.1 401 Unauthorized");
}
