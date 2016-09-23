<?php

require '../model/Crud.php';
include('Mail.php');

if(!isset($_GET['email'])) {
    header("location: /");
    exit;
}

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

$email = htmlspecialchars($_GET['email']);

if(isset($_GET['token']) && isset($_GET['newpass'])) {
    $newpass = urldecode($_GET['newpass']);
    $token = urldecode($_GET['token']);
    $model = new Crud();
    $model->select = "id";
    $model->from = "users";
    $model->condition = "email='$email' and validation_string='$token'";
    $model->Read();
    if(isset($model->rows[0])) {
        $uid = $model->rows[0]['id'];

        $model->update = "users";
        $model->set = "password='". password_hash($newpass, PASSWORD_BCRYPT). "'";
        $model->set .= ", validation_string = ''";
        $model->condition = "id=$uid";
        $model->Update();
        $result["message"] = "Password has been reset.";
    } else {
        $result["message"] = "Invalid password reset request.";
    }

} else {
    $model = new Crud();
    $model->select = "id";
    $model->from = "users";
    $model->condition = "email='$email'";
    $model->Read();

    if(isset($model->rows)) {
        $r = RandomString();
        $model = new Crud();
        $model->update = "users";
        $model->set = "validation_string='". $r. "'";
        $model->condition = "email='$email'";
        $model->Update();

        $from = 'NetPivot DO_NOT_REPLY <noreply@netpivot.io>';
        $to = "<$email>";
        $subject = 'NetPivot Password Reset';
        $body = "Hi,\n\nPlease use the following link to reset your password.\nhttp://". $_SERVER['HTTP_HOST']. "/reset_pass.php?email=". urlencode($email). "&token=". urlencode($r);

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

    }
    $result["message"] = "Email an email has been sent to your inbox with a link to reset your password.";
}

echo json_encode($result);
?>



