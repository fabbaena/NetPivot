<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/functions.php';
require_once dirname(__FILE__) .'/../model/Event.php';

$username = get_username($_POST);
$password = get_password($_POST);

$user = new User();

if ($user->login($username, $password)) { //Establish all the parameters for the session
    $session = new StartSession();
    $session->set('usuario',$user->name);
    $session->set('loged', true);
    $session->set('id',$user->id);
    $session->set('type', $user->type);
    $session->set('max_files', $user->max_files);
    $session->set('roles', $user->roles);
    $session->set('starturl', $user->roles[0]->starturl);
    $session->set('user', $user);
    $starturl = $user->roles[0]->starturl;

    new Event($user, "Logged in.", 1);
    header("location: ../". $starturl);
} else {
    new Event($user, "Bad username or password for ". $username);
    header ('location:../index.php?error=1');
}
        