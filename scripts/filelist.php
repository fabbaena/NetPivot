<?php

require_once dirname(__FILE__) .'../content/model/FileManager.php';

if(!isset($argv[1])) {
    print("Error - define a start date.\n");
    return 1;
}
$filelist = new FileList(array('condition'=>array("upload_time", "<", $argv[1])));
foreach($filelist->files as $f) {
    print("$f->uuid\n");
}

?>