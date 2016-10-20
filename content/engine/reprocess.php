<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../model/StartSession.php';
require_once '../model/FileManager.php';
require_once '../model/UserList.php';
require_once '../model/Conversions.php';
require_once '../engine/Config.php';

$session    = new StartSession();
$user = $session->get('user');

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) { 
    header('location: /'); 
    exit();
}

$uuid  = htmlspecialchars($_GET['file']);


$c = new Config($uuid);
$c->convert_orphan(true);

try {
    $file = new FileManager(array('uuid' => $uuid));
    if(!$file->load('uuid')) 
        throw new Exception('Cannot load file data.');

    if(file_exists($c->stats_file()))
        unlink($c->stats_file());
    if(file_exists($c->error_file()))
        unlink($c->error_file());
    if(file_exists($c->json_file()))
        unlink($c->json_file());
    if(file_exists($c->ns_file()))
        unlink($c->ns_file());

    $pwd = exec($c->command(), $pwd_out,$pwd_error); 

    $conversion = new Conversion(array('files_uuid' => $uuid));
    if(!$conversion->load('files_uuid')) 
        throw new Exception('Cannot load conversion data.');

    $conversion->id = null;
    $file->delete();
    if(!$file->save()) 
        throw new Exception('Cannot save file data.');
    if(!$conversion->save()) 
        throw new Exception('Cannot save conversion data');

    $string = file_get_contents($c->json_file());
    $json_a = json_decode($string, true);
    $conversion->loadJSON($json_a);
    if(!$conversion->saveData()) 
        throw new Exception('Cannot save JSON data');

    header ('location:../dashboard/content.php');

} catch (Exception $ex) {
    header ('location: '. $_SERVER['HTTP_REFERER'].'?fatal');
    error_log($ex->getMessage());
}

