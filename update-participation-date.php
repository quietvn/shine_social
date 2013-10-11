<?php
set_include_path(get_include_path() . PATH_SEPARATOR . './libs');

include_once 'configs.php';
include_once 'Misfit/Exps.php';
include_once 'Misfit/Users.php';
include_once 'Misfit/Twitter.php';
include_once 'Misfit/Mongo.php';

$users_db = new MisfitUsers();
$users = $users_db->getAllByScore();

foreach ($users as $user) {
	$log = exec("cat log/shinelabs.log | grep {$user['id_twitter']} | head");
	$log_arr = explode(" ", $log);
	$start_date = $log_arr[0];
	
	if (!empty($start_date)) {
		$query = "
			UPDATE users 
			SET start_date='{$start_date}'
			WHERE id = {$user['id']}
		";
		Logger::log("Update user {$user['id_twitter']} start date to {$start_date}");
		$users_db->query($query);
	} else {
		Logger::log("Cannot find start date of user {$user['id_twitter']}");
	}
}

?>