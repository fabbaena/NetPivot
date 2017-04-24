
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/Conversions.php';
require_once dirname(__FILE__) .'/functions.php';

$session   = new StartSession();
$user   = $session->get('user');

$result["message"] = "Unknown error";
$result["status"] = "error";

if(!$user || !($user->has_role("Engineer") || $user->has_role("Sales"))) {
    $result["message"] = "Access Denied";
    echo json_encode($result);
    exit();
}

$conversionid = get_int($_GET, 'id');
$projectid = get_int($_GET, 'projectid');
$action = get_validstring($_GET, 'action');

if(!isset($action)) {

    $result["message"] = "Action missing";

} else if($action == 'edit' && isset($conversionid)) {

    $conversion = new Conversion(array("id" => $conversionid));
    $conversion->load('id');
    $conversion->projectid = $projectid;

    $conversion->update();
    $result["message"] = "Quote has been Modified. ";
    $result["status"] = "ok";
    $result["idInsert"] = $conversion->id;

} 

echo json_encode($result);

?>

