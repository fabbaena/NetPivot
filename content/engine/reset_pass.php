<?php
require_once dirname(__FILE__) .'/../model/UserList.php';
include('Mail.php');


$email = get_email($_GET);
np_check(isset($email), "Data entered is invalid.");

$token = get_token($_GET);
$password = get_password($_GET);

if(isset($token) && isset($password)) {

    $u = new User(array(
        'email' => $email, 
        'validation_string' => $token,
        'password' => $password));
    np_check($u->valid_token(), "Invalid password reset request.");
    $u->setPassword();

    $result["message"] = "Password has been reset.";

} else if(isset($email)) {
    $u = new User(array('email' => $email));
    $u->load2(array('email'));

    np_check($u->load2(array('email')), "If you already have an account an email ".
        "has been sent to your inbox with a link to reset your password.");

    $r = $u->new_token();

    $from = 'NetPivot DO_NOT_REPLY <noreply@netpivot.io>';
    $to = "<$email>";
    $subject = 'NetPivot Password Reset';
    $body = "Hi,\n\nPlease use the following link to reset your password.\n".
            "https://". $_SERVER['HTTP_HOST']. "/reset_pass.php?email=". 
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
            'password' => 'U&0MQ7/4(f}_M.'
        ));

    $mail = $smtp->send($to, $headers, $body);

    $result["message"] = "If you already have an account an email has been ".
        "sent to your inbox with a link to reset your password.";
}

echo json_encode($result);
?>



