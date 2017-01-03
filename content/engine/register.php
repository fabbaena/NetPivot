<?php

require_once '../model/UserList.php';
require_once '../model/DomainList.php';
require_once '../model/Event.php';
require_once 'functions.php';
include('Mail.php');

$email = get_email($_GET);
$company_id = get_validstring($_GET, 'company_id');
$company = get_validstring($_GET, 'company');
$position = get_validstring($_GET, 'position');
$firstname = get_validstring($_GET, 'firstname');
$lastname = get_validstring($_GET, 'lastname');
if(isset($_GET['saml'])) {
    $saml = $_GET['saml'];
} else {
    $saml = false;
}

np_check(isset($email), "Data entered is invalid.");
np_check(isset($company), "Data entered is invalid.");
np_check(isset($position), "Data entered is invalid.");
np_check(isset($firstname), "Data entered is invalid.");
np_check(isset($lastname), "Data entered is invalid.");


$domain = substr($email, strpos($email, "@") + 1);


$validuser = new User(array('name' => $email));
np_check(!$validuser->load(), "You have already an account.");

$validdomain = new Domain(array('name' => $domain));
np_check($validdomain->load(), "Your company is not registered ".
    "to be able to use NetPivot. Please contact us at info@samanagroup.co");


$r = RandomString();

$role = new Role(array("id" => 3));
$user = new User(array(
    "name" => $email,
    "type" => "user",
    "max_files" => 100,
    "max_conversions" => 100,
    "email" => $email,
    "validation_string" => $r,
    "company" => $company,
    "company_id" => $company_id,
    "position" => $position,
    "firstname" => $firstname,
    "lastname" => $lastname));
$user->addRole($role);
$user->save(true);

new Event($user, "Registered");

if(!$saml) {
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
}

$result["message"] = "Account has been created. ". 
    ($saml ? "Try to login again." : "Please check your email to activate your account.");
echo json_encode($result);
?>



