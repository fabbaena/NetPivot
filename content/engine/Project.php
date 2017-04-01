
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';
require_once dirname(__FILE__) .'/../model/Project.php';
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

$projectid = get_int($_POST, 'id');
$projectname = get_validstring($_POST, 'name');
$description = isset($_POST['description']) ? $_POST['description'] : "";
$file = isset($_POST['attachment']) ? $_POST['attachment'] : null;
$customerid = get_int($_POST, 'customerid');
$createdate = date("Y-m-d");
$usercreate = get_validstring($_POST, 'usercreate');
$userupdate = get_validstring($_POST, 'userupdate');
$total = get_int($_POST, 'total');
$updatedate = date("Y-m-d");
$ip = $_SERVER["REMOTE_ADDR"];
$action = get_validstring($_POST, 'action');
$opportunityid = isset($_POST['opportunityid']) ? $_POST['opportunityid'] : null;


if(isset($file)) {
    mkdir(dirname(__dir__)."/Uploads/".$projectname, 0700);

    $upload_dir = dirname(__dir__).'/Uploads/'.$projectname.'/';
    $uploaded_file = $upload_dir . basename($_FILES['attachment']['name']);
    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploaded_file)) {
        $attachment='Uploads/'.$projectname.'/'.$_FILES['attachment']['name'];
    } else {
        
    }    
}


if(!isset($action)) {

    $result["message"] = "Action missing";

} else if($action == 'edit' && isset($projectid)) {

    $project = new Project(array(
        "id" => $projectid,
        "name" => $projectname,
        "description" => $description,
        "customerid" => $customerid,
        "userupdate" => $userupdate,
        "updatedate" => $updatedate,
        "total" => $total,
        "opportunityid" => $opportunityid
       
    ));

    $project->edit();
    $result["message"] = "Quote has been Modified. ";
    $result["status"] = "ok";
    $result["idInsert"] = $projectid;

} else if ($action == 'delete' && isset($projectid)) {

    $project = new Project(array("id" => $projectid));
    $project->load();
    if($project->usercreate != $user->id) {
        $result["message"] = "You can only delete your own quotes.";
    } else if($project->delete()) {
        $result["message"] = "Project deleted";
        $result["status"] = "ok";
    } else {
        $result["message"] = "An error occurred deleting project";
        $result["status"] = "error";
    }

} else if ($action == 'create') {

    if (isset($projectname)) {
        $project = new Project(array(
            "name" => $projectname,
            "description" => $description,
            "customerid" => $customerid,
            "createdate" => $createdate,
            "usercreate" => $usercreate,
            "ip" => $ip,
            "attachment"=>$attachment,
            "total" => 0,
            "opportunityid" => $opportunityid
        ));
        $result["message"] = "Quote has been created. ";
        $result["idInsert"] = $project->save();
        $result["status"] = "ok";
    }
}

echo json_encode($result);

?>

