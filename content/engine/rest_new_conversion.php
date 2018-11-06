<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, 
    Access-Control-Allow-Methods, Content-Type,
    Access-Control-Allow-Headers');

$users_id = 1;

/* PUT data comes in on the stdin stream */
$putdata = json_decode(file_get_contents("php://input"));

echo $putdata->file;