<?php
require_once dirname(__FILE__) .'/../model/FileManager.php';
require_once dirname(__FILE__) .'/../model/Conversions.php';
require_once dirname(__FILE__) .'/../model/AuthJwt.php';

function getListFiles($user_id){
    $file_list = new FileList($records=['users_id' => $user_id]);

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

            $conv = new Conversion(['files_uuid' => $file->uuid, 'users_id' => $user_id]);
            $current['converted'] = $conv->load(['files_uuid', 'users_id']);

            $out[] = $current;
        }

        return json_encode($out);
    } else{
        return json_encode(
            ['message' => 'There are no converions for this user.']
        );
    }
}

$jwt = AuthJwt::getTokenFromHeaders(getallheaders());
$auth = new AuthJwt();
if ($auth->validJwt($jwt)){
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    $user_id = $auth->getUserId($jwt);
    echo getListFiles($user_id);

}else{
    header("HTTP/1.1 401 Unauthorized");
}
