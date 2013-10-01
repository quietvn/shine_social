<?php
set_include_path(get_include_path() . PATH_SEPARATOR . './libs');

include_once 'configs.php';
include_once 'Logger.php';
include_once 'Misfit/Mongo.php';
include_once 'Misfit/Users.php';
include_once 'Misfit/Exps.php';
include_once 'Misfit/ExpChecker2.php';
//include_once 'Misfit/ExpChecker3.php';

Logger::log("STARTED");

$users_db = new MisfitUsers();
$users_db->syncAll();

$exp_db = new MisfitExps();
$exps = $exp_db->getAll();

$users = $users_db->getAllByGroup();

foreach ($exps as $exp) {
	$id_exp = $exp["id_exp"];
	$id_group = $exp["id_group"];
	$id_twitter = $exp["id_twitter"];
	
	if (empty($users[$id_group]))
		continue;
	
	Logger::log("Processing exp #$id_exp on group #$id_group of ".sizeof($users[$id_group])." members");
		
	$classname = 'MisfitExpChecker' . $id_exp;
	$checker = new $classname($id_twitter);
	$checker->checkEvent($users[$id_group]);
}

