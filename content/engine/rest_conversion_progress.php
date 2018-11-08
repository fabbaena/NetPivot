<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, 
    Access-Control-Allow-Origin, Content-Type, 
    Access-Control-Allow-Methods');

require_once '../model/Crud.php';


$users_id = 1;

// Obtain incoming request
$incoming = json_decode(file_get_contents("php://input"));

$uuid = $incoming->id;

$conn = new CRUD();
$conn->select = "conv_progress";
$conn->from = "files";
$conn->condition = "users_id=$users_id AND files_uuid='$uuid'";
$conn->Read();

if(count($conn->rows) != 0){
    echo json_encode(rows[0]);
} else{
    echo json_encode(['message' => 'No such id']);
}