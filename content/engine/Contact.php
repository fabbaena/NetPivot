
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/Contact.php';
require_once dirname(__FILE__) .'/../model/DomainList.php';
require_once dirname(__FILE__) .'/functions.php';

$session   = new StartSession();
$user   = $session->get('user');

$result["message"] = "Unknown error";
$result["status"] = "error";

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    $result["message"] = "Access Denied";
    echo json_encode($result);
    exit();
}

$contactid = get_int($_POST, 'id');
$contactname = get_validstring($_POST, 'name');
$position = get_validstring($_POST, 'position');
$phone = get_validstring($_POST, 'phone');
$usercreate = get_validstring($_POST, 'usercreate');
$userupdate = get_validstring($_POST, 'userupdate');
$createdate = date("Y-m-d");
$updatedate = date("Y-m-d");
$ip = $_SERVER["REMOTE_ADDR"];
$customerid = get_int($_POST, 'customerid');
$action = get_validstring($_POST, 'action');


if(!isset($action)) {

    $result["message"] = "Action missing";

} else if($action == "delete" && isset($contactid)) {

    $contact = new Contact(array("id" => $contactid));
    $contact->load();
    error_log($contact->usercreate);
    if($contact->usercreate != $user->id) {

        $result["message"] = "You can only delete your own contacts.";

    } else if($contact->delete()) {

        $result["message"] = "Contact Deleted";
        $result["status"] = "ok";

    } else {

        $result["message"] = "Contact not deleted";
        $result["status"] = "error";

    }

} else if ($action == 'edit' && isset($contactid)) {

    $contact = new Contact(array(
        "id" => $contactid,
        "name" => $contactname,
        "position" => $position,
        "phone" => $phone,
        "userupdate"=>$userupdate,
        "updatedate"=>$updatedate,
        "customerid" => $customerid
    ));
    $contact->edit();
    $result["message"] = "Contact has been updated. ";
    $result["idInsert"] = $contactid;
    $result['status'] = "ok";

} else if($action == 'create') {

    if (isset($contactname)) {
        $contact = new Contact(array(
            "name" => $contactname,
            "position" => $position,
            "phone" => $phone,
            "usercreate"=>$usercreate,
            "createdate"=>$createdate,
            "customerid"=>$customerid,
            "ip"=>$ip));

        $insertid = $contact->save();
        
        $result["message"] = "Contact has been created. ";
        $result["idInsert"] = $insertid;
        $result['status'] = "ok";
    } else {
        $result["message"] = "Contact name not defined.";
    }
}

echo json_encode($result);

?>

