<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, 
    Access-Control-Allow-Origin, Content-Type, 
    Access-Control-Allow-Methods');

require_once '../model/FileManager.php';
require_once '../model/Conversions.php';

$users_id = 1;

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