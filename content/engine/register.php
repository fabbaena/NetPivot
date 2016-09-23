<?php

require '../model/Crud.php';
require '../model/UserList.php';
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

if(isset($_GET['email']) && isset($_GET['company']) && 
        isset($_GET['position']) && isset($_GET['firstname']) && 
        isset($_GET['lastname'])) {
    $email = urldecode($_GET['email']);
    $company = urldecode($_GET['company']);
    $position = urldecode($_GET['position']);
    $firstname = urldecode($_GET['firstname']);
    $lastname = urldecode($_GET['lastname']);

    $domain = substr($email, strpos($email, "@") + 1);

    $model = new Crud();

    $model->select = "id";
    $model->from = "users";
    $model->condition = "name='$email'";
    $model->Read();
    if(isset($model->rows[0])) {
        $result["message"] = "You have already an account.";
    } else {
        $model->select = "id";
        $model->from = "domains";
        $model->condition = "name='$domain'";
        $model->Read();

        if(!isset($model->rows[0])) {
            $result["message"] = "Your company is not registered to be able to use NetPivot. Please contact us at info@samanagroup.co";
        } else {
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
            $body = "Hi,\n\nPlease use the following link to set your password.\nhttp://". $_SERVER['HTTP_HOST']. "/reset_pass.php?email=". urlencode($email). "&token=". urlencode($r);

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
        }
    }


} else {
    $result["message"] = "Information incomplete.";
}

echo json_encode($result);
?>



