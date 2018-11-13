<?php

require_once dirname(__FILE__) .'/../vendor/autoload.php';
require_once dirname(__FILE__) .'/../model/Crud.php';
require_once dirname(__FILE__) .'/../model/UserList.php';

use Firebase\JWT\JWT;

/**
 * Abstraction of a Json Web Token
 *
 * JWT :
 *  - Header {"alg": "HS256",
 *            "typ": "JWT" }
 *  - Payload {"user_id": (int),
 *             "exp": epoc_now + $jwt_valid_secs (int)}
 *  - Signature HMACSHA256(base64UrlEncode(Header) + "." + base64UrlEncode(Payload),
 *                         $jwt_secret)
 */
class AuthJwt {

    function __construct(){
        $model = new CRUD();
        $model->select = "*";
        $model->from = "settings";
        $model->Read2();

        if(!isset($model->fetchall[0])){
            $this->jwt_valid_secs = 300;
            $this->jwt_secret = "Unsecure secret";
        } else {
            $this->jwt_valid_secs = $model->fetchall[0]['jwt_lifetime_secs'];
            $this->jwt_secret = $model->fetchall[0]['jwt_secret'];
        }
    }

    /* Gets the user_id from `$jwt`
     *  @thows Firebase\JWT\SignatureInvalidException
     *  @thows Firebase\JWT\ExpiredException
     *  @thows Firebase\JWT\BeforeValidException
     */
    public function getUserId($jwt){
        $decoded = JWT::decode($jwt, $this->jwt_secret, array('HS256'));
        return $decoded->user_id;
    }

    public function validJwt($jwt){
        try{
            $decoded = JWT::decode($jwt, $this->jwt_secret, array('HS256'));
        }catch(Firebase\JWT\SignatureInvalidException $e){
            return false;
        }catch(Firebase\JWT\ExpiredException $e){
            return false;
        }catch(Firebase\JWT\BeforeValidException $e){
            return false;
        }catch(Exception $e){
            return false;
        }
        return true;
    }

    public function signIn($username, $password){
        $user = new User();
        if ($user->login($username, $password)){
           $token = array("exp" => time() + $this->jwt_valid_secs,
                          "user_id" => $user->id);
           return $jwt = JWT::encode($token, $this->jwt_secret);
        }
    }

    public function renew($jwt){
       if($this->validJwt($jwt)){
          $token = array("exp" => time() + $this->jwt_valid_secs,
                         "user_id" => $this->getUserId($jwt));
          return $jwt = JWT::encode($token, $this->jwt_secret);
       }
    }

    /* The token must be in the Authorization header
     * under de Bearer schema.
     */
    public static function getTokenFromHeaders($headers){
        $authorization_header = $headers['Authorization'];
        list($token) = sscanf($authorization_header, 'Bearer %s');
        return $token;
    }
}

?>
