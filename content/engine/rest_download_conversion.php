<?php

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, 
    Access-Control-Allow-Origin, Content-Type, 
    Access-Control-Allow-Methods');

require_once '../model/Conversions.php';

$users_id = 1;

// Obtain incoming request
$incoming = json_decode(file_get_contents("php://input"));

$uuid = $incoming->id;

$conv = new Conversion(['files_uuid' => $uuid, 'users_id' => $users_id]);

if($conv->load(['files_uuid', 'users_id'])){
    header('Content-Type: text/plain');
    readfile($conv->converted_file);

} else{
    header('Content-Type: application/json');
    echo json_encode([
        'message' => 'No such conversion.'
    ]);
}