<?php

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/UserList.php';

$session = new StartSession();
$user = $session->get('user');

if(!$user || !isset($_GET['term'])) {
    header('location: ../');
    exit();
}
$term = $_GET['term'];

if($user->has_role("Company Admin")) {
	$data = new UserEventList($user->company_id, $term);
	$out = $data->userlist;
} else {
	$out = array($user->name);
}

echo json_encode($out);

?>