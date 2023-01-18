<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) .'../content/model/FileManager.php';

if(!isset($argv[1])) {
    print("Error - UUID of the conversion is needed.\n");
    exit();
}
$uuid=$argv[1];

$output = null;
$retval = null;
exec('id -u', $output, $retval);
if($output[0] != "0") {
    print("Error - This command must be run as root.\n");
    exit(1);
}

$file = new FileManager(array('uuid' => $uuid));
$file->load('uuid');

print("Ready to delete $uuid.\n");
$file->toString();

if(!$file->delete()) {
    print("Error - Unable to delete database records for $uuid.\n");
    exit(1);
}
$allfiles = glob("/var/www/files/$uuid*", 0);
foreach($allfiles as $file) {
    $status=unlink($file);
    if(!$status){
        print("Error - Unable to delete file $file.\n");
    }
}

?>
