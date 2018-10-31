<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once '../model/FileManager.php';
require_once '../model/Conversions.php';

$users_id = '1';

$file_list = new FileList($records=['users_id' => $users_id]);

if($file_list->count > 0){
    $files = $file_list->files;

    $out = [];
    foreach($files as $file){
        $current = [
	       'id' => $file->uuid, 
	       'filename' => $file->filename,
	       'upload_time' => $file->upload_time,
	       'size' => $file->size
	];
	
	$conv = new Conversion(['files_uuid' => $file->uuid, 'users_id' => $users_id]);
	$current['converted'] = $conv->load(['files_uuid', 'users_id']);

	$out[] = $current;
    }

    echo json_encode($out);
} else{
    echo json_encode(
        ['message' => 'There are no converions for this user.']
    );
};

