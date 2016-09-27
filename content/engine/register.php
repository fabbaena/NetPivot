<?php

require '../model/Crud.php';
require '../model/UserList.php';
require 'functions.php';
include('Mail.php');

np_check(isset($_GET['email']), "Incomplete information");
np_check(isset($_GET['company']), "Incomplete information");
np_check(isset($_GET['position']), "Incomplete information");
np_check(isset($_GET['firstname']), "Incomplete information");
np_check(isset($_GET['lastname']), "Incomplete information");

$email = urldecode($_GET['email']);
$company = urldecode($_GET['company']);
$position = urldecode($_GET['position']);
$firstname = urldecode($_GET['firstname']);
$lastname = urldecode($_GET['lastname']);

$validemailpat = '/^[._A-z0-9]{1,}@[._A-z0-9]{1,}\.[A-z]{1,}$/';
$validstringpat = '/^[.A-z0-9 \']{1,30}$/';

np_check(preg_match($validemailpat, $email), "Data entered is invalid.");
np_check(preg_match($validstringpat, $company), "Data entered is invalid.");
np_check(preg_match($validstringpat, $firstname), "Data entered is invalid.");
np_check(preg_match($validstringpat, $lastname), "Data entered is invalid.");

$domain = substr($email, strpos($email, "@") + 1);

$model = new Crud();

$model->select = "id";
$model->from = "users";
$model->condition = "name='$email'";
$model->Read();
np_check(!isset($model->rows[0]), "You have already an account.");

$model->select = "id";
$model->from = "domains";
$model->condition = "name='$domain'";
$model->Read();
np_check(isset($model->rows[0]), "Your company is not registered to be able to use NetPivot. Please contact us at info@samanagroup.co");

$r = RandomString();

$role = new Role(array(
    "roleid" => 3,
    "rolename" => "",
    "starturl" => ""));
$user = new User(array(
    "name" => $email,
    "type" => "user",
    "max_files" => 100,
    "max_conversions" => 100,
    "email" => $email,
    "validation_string" => $r,
    "company" => $company,
    "position" => $position,
    "firstname" => $firstname,
    "lastname" => $lastname));
$user->addRole($role);
$user->save(true);

$from = "NetPivot DO_NOT_REPLY <noreply@netpivot.io>";
$to = "<$email>";
$subject = 'NetPivot Account Created';
$body = "Hi,\n\nPlease use the following link to set your password.\n".
        "http://". $_SERVER['HTTP_HOST']. "/reset_pass.php?email=". 
        urlencode($email). "&token=". urlencode($r);

$headers = array(
    'From' => $from,
    'To' => $to,
    'Subject' => $subject
);
$smtp = Mail::factory('smtp', array(
        'host' => 'ssl://smtp.gmail.com',
        'port' => '465',
        'auth' => true,
        'username' => 'noreply@netpivot.io',
        'password' => 'U&0MQ7/4(f}_M'
    ));

$mail = $smtp->send($to, $headers, $body);

$result["message"] = "Account has been created. Please check your email to activate your account.";
echo json_encode($result);
?>



