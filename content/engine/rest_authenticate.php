<?php

require_once dirname(__FILE__) .'/../model/AuthJwt.php';

$username = get_username($_POST);
$password = get_password($_POST);

$auth = new AuthJwt();
$token = $auth->signIn($username,$password);
if(isset($token)){
    echo $token; //en formato JSON
}else{
    header("HTTP/1.1 401 Unauthorized");
}

?>
