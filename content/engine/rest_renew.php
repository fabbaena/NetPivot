<?php

require_once dirname(__FILE__) .'/../model/AuthJwt.php';

$token = AuthJwt::getTokenFromHeaders(getallheaders());

$auth = new AuthJwt();
$new_token = $auth->renew($token);
if(isset($new_token)){
    echo $new_token;
}else{
    header("HTTP/1.1 401 Unauthorized");
}

?>
