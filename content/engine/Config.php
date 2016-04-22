<?php

$path_pivot = '/opt/netpivot/';
$path_files = '/var/www/np/np/content/dashboard/files/';

$f5conv       = $path_pivot. 'f5conv';
$f5_file      = $uuid;
$ns_file      = $uuid. '_.conf';
$error_name   = $uuid. '_error.txt';
$csv_name     = $uuid. '_stats.csv';

$p_f5_file    = $path_files. $f5_file;
$p_ns_file    = $path_files. $ns_file;
$p_error_name = $path_files. $error_name;
$p_csv_name   = $path_files. $csv_name;

$command   = "$f5conv -f $p_f5_file -e $p_error_name -C $p_csv_name -O $p_ns_file";

?>