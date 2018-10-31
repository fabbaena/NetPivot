<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once '../model/FileManager.php';


$users_id = '1';

$file_list = new FileList($records=['users_id' => $users_id]);

if($file_list->count > 0){
    $files = $file_list->files;

    $out = [];
    foreach($files as $file){
        $out[] = ['id' => $file->uuid,  'filename' => $file->filename];
    }

    echo json_encode($out);
} else{
    echo json_encode(
        ['message' => 'There are no converions for this user.']
    );
};

