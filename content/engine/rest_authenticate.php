<?php

require_once dirname(__FILE__) .'/../model/AuthJwt.php';

 // Obtain incoming request
$incoming = json_decode(file_get_contents("php://input"));
$username = $incoming->username;
$password = $incoming->password;

$auth = new AuthJwt();
$token = $auth->signIn($username,$password);
if(isset($token)){
    echo json_encode(array("token" => $token));
}else{
    header("HTTP/1.1 401 Unauthorized");
}

?>
