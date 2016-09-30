<?php


function RandomString()
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charlen = strlen($characters) - 1;
    $randstring = '';
    for ($i = 0; $i < 50; $i++) {
        $randstring .= $characters[rand(0, $charlen)];
    }
    return $randstring;
}

function np_check($b, $message) {
    if(!($b)) {
        $result["message"] = $message;
        echo json_encode($result);
        exit(0);
    }
}

function get_username(&$data) {
    $validemailpat = '/^[._A-z0-9]{1,}@[._A-z0-9]{1,}\.[A-z]{1,}$/';
    $validusername = '/^[._A-z0-9]{1,20}/';
    if(!isset($data['username'])) return null;
    $username = urldecode($data['username']);
    if(!preg_match($validemailpat, $username) && 
    	!preg_match($validusername, $username)) return null;

    return $username;
}

function get_email(&$data) {
    $validemailpat = '/^[._A-z0-9]{1,}@[._A-z0-9]{1,}\.[A-z]{1,}$/';

    if(!isset($data['email'])) return null;
    $email = urldecode($data['email']);
    if(!preg_match($validemailpat, $email)) return null;

    return $email;
}

function get_token(&$data) {
    $validtoken = '/^[A-z0-9]{50}$/';
    if(!isset($data['token'])) return null;
    $token = urldecode($data['token']);
    if(!preg_match($validtoken, $token)) return null;

    return $token;
}

function get_password(&$data) {
    $validpassword = '/^[!-}]{8,20}$/';
    if(!isset($data['password'])) return null;
    $password = urldecode($data['password']);
    if(!preg_match($validpassword, $password)) return null;

    return $password;
}

function get_validstring(&$data, $name) {
    $validstring = '/^[.A-z0-9 \']{1,30}$/';
    if(!isset($data[$name])) return null;
    $string = urldecode($data[$name]);
    if(!preg_match($validstring, $string)) return null;

    return $string;
}

function get_domain(&$data) {
    $validstring = '/^[.A-z0-9]{1,50}$/';
    if(!isset($data['domainname'])) return null;
    $string = urldecode($data['domainname']);
    if(!preg_match($validstring, $string)) return null;

    return $string;
}

function get_int(&$data, $name) {
	$validstring = '/^[0-9]{1,6}/';
	if(!isset($data[$name])) return null;
	$int = urldecode($data[$name]);
	if(!preg_match($validstring, $int)) return null;

	return $int + 0;
}
?>