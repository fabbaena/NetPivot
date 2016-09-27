<?php

require '../model/Crud.php';
require 'functions.php';
include('Mail.php');


$email = get_email($_GET);
np_check(isset($email), "Data entered is invalid.");

$token = get_token($_GET);
$password = get_password($_GET);

if(isset($token) && isset($password)) {
    $model = new Crud();
    $model->select = "id";
    $model->from = "users";
    $model->condition = "email='$email' and validation_string='$token'";
    $model->Read();
    np_check(isset($model->rows[0]), "Invalid password reset request.");
    $uid = $model->rows[0]['id'];

    $model->update = "users";
    $model->set = "password='". password_hash($password, PASSWORD_BCRYPT). "'";
    $model->set .= ", validation_string = ''";
    $model->condition = "id=$uid";
    $model->Update();
    $result["message"] = "Password has been reset.";

} else {
    $model = new Crud();
    $model->select = "id";
    $model->from = "users";
    $model->condition = "email='$email'";
    $model->Read();

    np_check(isset($model->rows[0]), "If you already have an account an email ".
        "has been sent to your inbox with a link to reset your password.");

    $r = RandomString();
    $model = new Crud();
    $model->update = "users";
    $model->set = "validation_string='". $r. "'";
    $model->condition = "email='$email'";
    $model->Update();

    $from = 'NetPivot DO_NOT_REPLY <noreply@netpivot.io>';
    $to = "<$email>";
    $subject = 'NetPivot Password Reset';
    $body = "Hi,\n\nPlease use the following link to reset your password.\n".
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

    $result["message"] = "If you already have an account an email has been ".
        "sent to your inbox with a link to reset your password.";
}

echo json_encode($result);
?>



