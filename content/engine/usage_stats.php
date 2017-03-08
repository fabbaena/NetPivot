<?php

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/Crud.php';
require_once dirname(__FILE__) .'/../model/UserList.php';

$session = new StartSession();
$user = $session->get('user');
$admin = $user->has_role("Company Admin");
$company_id = $user->company_id;

$days = get_int($_GET, 'days');
$event_type = get_int($_GET, 'etype');

if(!$user || !$admin || $days < 1  || $days > 500 || $event_type < 1) {
    header('location: ../');
    exit();
}

$db = new Crud();
$db->select = 'u.name as username, c.name as company, count(e.*) as event_count';
$db->from = 'events e, users u, companies c ';
$db->condition = "e.user_id = u.id and ".
	"u.company_id=c.id and ".
	"timestamp > current_date - $days and ".
	"c.id = $company_id and ".
	"event_code = $event_type";
$db->groupby = "u.name, c.name";
$db->ReadObject();



$out['status'] = 'ok';
$out['stats'] = $db->rows;

echo json_encode($out);

?>