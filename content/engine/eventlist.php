<?php

require_once dirname(__FILE__) .'/../model/StartSession.php';
require_once dirname(__FILE__) .'/../model/Event.php';

$session = new StartSession();
$user = $session->get('user');

if(!$user) {
    header('location: ../');
    exit();
}

$filters = array();
$out = array();

$filters['user_id'] =  $user->id;
$filters['company_id'] = $user->company_id;

$out['status'] = 'ok';

$filters['oldest_timestamp'] = get_timestamp($_GET, 'oldest_timestamp'). " 00:00:00";
$filters['newest_timestamp'] = get_timestamp($_GET, 'newest_timestamp'). " 23:59:59";
$filters['event_code'] = get_int($_GET, 'event_code');
if($user->has_role("Company Admin")) {
	$filters['user_id'] = get_int($_GET, 'user_id');
}

if($out['status'] == 'ok') {
	$eventlist = new EventList($filters);
	$out['events'] = &$eventlist->events;
}

echo json_encode($out);

?>