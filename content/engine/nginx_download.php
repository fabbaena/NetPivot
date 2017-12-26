<?php
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/Event.php';
require_once dirname(__FILE__) .'/Config.php';

$session = new StartSession();
$user    = $session->get('user');

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: ../');
    exit();
}

if(!isset($_GET['uuid']) || !isset($_GET['file'])) {
    print "Invalid Request";
    exit(1);
}
$uuid = $_GET['uuid'];
$file = $_GET['file'];
$outfilename = $session->get('upload_file_name').explode('.')[0];

if($file == 'config') {
    $filename = "{$uuid}_ns.conf";
    $outfilename .= "_ns.conf";
} else if($file == 'error') {
    $filename = "{$uuid}_error.txt";
    $outfilename .= "_error.txt";
} else {
    print "Invalid Request";
    exit(1);
}

$f = fopen("/var/www/nginx_files/{$filename}", "r") or die("Unable to open file!");


header('Content-Type: text/plain');
header("Content-Disposition: attachment; filename=\"{$outfilename}\"");
echo fread($f,filesize("/var/www/nginx_files/{$filename}"));
fclose($f);
?>