<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, 
    Access-Control-Allow-Origin, Content-Type, 
    Access-Control-Allow-Methods');

require_once '../model/FileManager.php';

$users_id = 1;

// Obtain incoming request
$incoming = json_decode(file_get_contents("php://input"));

$uuid = $incoming->id;

$fm = new FileManager(['users_id' => $users_id, 'uuid' => $uuid]);

if($fm->load(['uuid', 'users_id'])){

    $out = [
        'id' => $fm->uuid,
        'filename' => $fm->filename,
        'conversion-date' => $fm->upload_time,
        'size' => $fm->size
    ];

    echo json_encode($out);
} else{
    echo json_encode([
        'message' => 'No such conversion.'
    ]);
}