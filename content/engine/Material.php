
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/Material.php';
require_once dirname(__FILE__) .'/../model/DomainList.php';
require_once dirname(__FILE__) .'/functions.php';

$session   = new StartSession();
$user   = $session->get('user');

$result["message"] = "Unknown Error";
$result["status"] = "error";

if(!$user || !($user->has_role("Engineer") || $user->has_role("Sales"))) {
    $result["message"] = "Access Denied";
    echo json_encode($result);
    exit();
}

$id = get_int($_POST, 'id');
$sku = get_validstring($_POST, 'sku');
$description = isset($_POST['description']) ? $_POST['description'] : null;
$quantity = get_int($_POST, 'quantity');
$price = get_int($_POST, 'price');
$projectid = get_int($_POST, 'projectid');
$action = get_validstring($_POST, "action");

if(!isset($action)) {

    $result["message"] = "Action missing";

} else if($action == "edit" && isset($id)) {

    $material = new Material(array(
        "id" => $id,
        "sku" => $sku,
        "description" => $description,
        "quantity" => $quantity,
        "price" => $price,
        "projectid" => $projectid,
        ));
    $return = $material->edit();
    if($return === true) {
        $result["message"] = "Line Updated";
        $result["status"] = "ok";
    } else {
        $result["message"] = $return;
        $result["status"] = "error";
    }
} else if($action == "delete" && isset($id)) {

    $material = new Material(array('id' => $id));
    $material->load();
    if($material->delete()) {
        $result["message"] = "Line has been deleted. ";
        $result["status"] = "ok";
    } else {
        $result["message"] = "There was an error deleting the line. ";
        $result["status"] = "error";
    }
} else if($action == "create") {
    $material = new Material(array(
        "sku" => $sku,
        "description" => $description,
        "quantity"=>$quantity,
        "price"=>$price,
        "projectid"=>$projectid
        ));
    if($material->save()) {
        $result["message"] = "Material has been created. ";
        $result["status"] = "ok";
    } else {
        $result["message"] = "There was an error creating the line.";
        $result["status"] = "error";
    }    

}

echo json_encode($result);
 
?>

