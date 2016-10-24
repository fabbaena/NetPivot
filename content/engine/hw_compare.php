<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/HWModel.php';

$session = new StartSession();
$user    = $session->get('user');

if(!($user && ($user->has_role("Engineer") || $user->has_role("Sales")))) {
    header('location: ../');
    exit();
}

try {
    if(!isset($_GET['brand'])) 
        throw new Exception("Did not receive data.");

    $brand = $_GET['brand'];

    if(isset($_GET['model']) && isset($_GET['type'])) {
        $model = $_GET['model'];
        $type  = $_GET['type'];

        $hw = new HWModel(array('brand' => $brand, 'model' => $model, 'type' => $type));
        $hw->load(array('brand', 'model', 'type'));
        $process["data"] = $hw;
    } elseif(isset($_GET['type'])) {
        $type = $_GET['type'];
        $hwl = new HWModelList(array('brand' => $brand, 'type' => $type));
        $hwl->load(array('brand', 'type'));
        $process["data"] = $hwl->get();
    } else {
        throw new Exception("Need a type to continue.");
    }



    $process["result"] = "ok";
    $process["message"] = "Data sent.";
} catch (Exception $ex) {
    $process["result"] = "error";
    $process["message"] = $ex->getMessage();
}

echo json_encode($process);