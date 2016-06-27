<?php

$uuid = !isset($uuid)?$sesion->get('uuid'):$uuid;
$path_pivot = '/var/www/html/dashboard/';
$path_files = '/var/www/files/';

$f5conv       = $path_pivot. 'f5conv';
$f5_file      = $uuid;
$ns_file      = $uuid. '_.conf';
$error_name   = $uuid. '_error.txt';
$csv_name     = $uuid. '_stats.csv';
$json_name    = $uuid. '.json';

$p_f5_file    = $path_files. $f5_file;
$p_ns_file    = $path_files. $ns_file;
$p_error_name = $path_files. $error_name;
$p_csv_name   = $path_files. $csv_name;
$p_json_name  = $path_files. $json_name;

$command   = "$f5conv -f $p_f5_file -e $p_error_name -C $p_csv_name -O $p_ns_file -J $p_json_name";

?>

