<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin,
    Content-Type, Access-Control-Allow-Methods, Access-Control-Allow-Headers,
    Autorization');

require_once '../model/FileManager.php';

$incoming = json_decode(file_get_contents("php://input"));

$uuid = $incoming['id'];

$fm = new FileManager(['uuid' => $uuid]);

if($fm->CheckFile()){
    $fm->loadChild();

    echo json_encode($fm->_conversion);
} else{
    echo json_encode(['message' => 'No such conversion.']);
}