<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/Config.php';
require_once dirname(__FILE__) .'/../model/UserList.php';

$session = new StartSession();
$user = $session->get('user');

if(!($user && $user->has_role("Engineer"))) {
    header('location: /'); 
    exit();
}
$uuid = $session->get('uuid');
$filename  = $session->get('filename');

$c = new Config($uuid);

$file = $c->ns_file();

header( "Content-Type: application/octet-stream");
header( "Content-Length: ".filesize($file));
header( "Content-Disposition: attachment; filename=${filename}_ns.conf");
readfile($file);
        