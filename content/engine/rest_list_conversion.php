<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once "../models/Crud.php";
require_once "../models/FileManager.php";

class FileList{
    public $files;
    public $users_id;
    public $count;

    public function _construct($record = null){
        if(isset($record)){
            foreach( $this as $key => $value){
                if(isset($record[$key])){
                    $this->key = $record[$key];
                }
            }
        }
        $this->count = 0;
        $this->files = [];

        $db = new Crud();
        $db->select = '*';
        $db->from = 'files';
        $db->condition = new Condition('users_id', '=', $this->users_id);
        $db->orderby = ['column' => 'upload_time', 'direction' => 'DESC'];
        $db->Read5();
        
        foreach ($db->rows as $row) {
            $this->count++;
            $fm = new FileManager($row);
            $fm->loadChild();
            array_push($this->files, $fm);
        }
    }
}


$users_id = '1';

$file_list = new FileList($records=['users_id' => $users_id]);

if($file_list->count > 0){
    $files = $file_list->files;

    $out = [];
    foreach($files as $file){
        $out[] = [$file->uuid => $file->filename];
    }

    echo json_encode($out);
} else{
    echo json_encode(
        ['message' => 'There are no converions for this user.']
    );
}