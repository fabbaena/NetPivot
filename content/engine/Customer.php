
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) . '/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/Customer.php';
require_once dirname(__FILE__) .'/functions.php';

$session   = new StartSession();
$user   = $session->get('user');

$result["message"] = "Unknown error";
$result["status"] = "error";

if (!($user && $user->has_role("Engineer"))) {
    $result["message"] = "Access Denied";
    echo json_encode($result);
    exit();
}

$customerid = get_int($_POST, 'id');
$customername = get_validstring($_POST, 'name');
$phone = get_validstring($_POST, 'phone');
$usercreate = get_validstring($_POST, 'usercreate');
$userupdate= get_validstring($_POST, 'userupdate');
$updatedate = date("Y-m-d");
$createdate = date("Y-m-d");
$ip = $_SERVER["REMOTE_ADDR"];
$action = get_validstring($_POST, 'action');
$deletecustomerid = get_int($_POST, 'id');

if(!isset($action)) {

    $result["message"] = "Action missing";

} else if($action == "delete" && isset($customerid)) {

    $customer = new Customer(array("id" => $customerid));
    $customer->load();
    if($customer->usercreate != $user->id) {

        $result["message"] = "You can only delete your own customers.";

    } else if($customer->delete()) {

        $result["message"] = "Customer Deleted";
        $result["status"] = "ok";

    } else {

        $result["message"] = "Customer not deleted";
        $result["status"] = "error";

    }

} else if ($action == 'edit' && isset($customerid)) {

    $customer = new Customer(array(
        "id" => $customerid,
        "name" => $customername,
        "phone" => $phone,
        "userupdate"=>$userupdate,
        "updatedate"=>$updatedate
    ));
    $customer->edit();
    $result["message"] = "Customer has been updated. ";
    $result["idInsert"] = $customerid;
    $result['status'] = "ok";

} else if($action == 'create') {

    if (isset($customername)) {
        $customer = new Customer(array(
            "name" => $customername,
            "phone" => $phone,
            "usercreate"=>$usercreate,
            "createdate"=>$createdate,
            "ip"=>$ip));

        $insertid = $customer->save();
        
        $result["message"] = "Customer has been created. ";
        $result["idInsert"] = $insertid;
        $result['status'] = "ok";
    }
}
echo json_encode($result);

?>

