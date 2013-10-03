<?php
set_include_path(get_include_path() . PATH_SEPARATOR . './libs');

include_once 'configs.php';
include_once 'Logger.php';
include_once 'Misfit/Mongo.php';
include_once 'Misfit/Users.php';
include_once 'Misfit/Exps.php';
include_once 'Misfit/ExpChecker2.php';
include_once 'Misfit/ExpChecker3.php';
include_once 'Misfit/Leaderboard.php';

Logger::log("STARTED");

$exp_db = new MisfitExps();
$exps = $exp_db->getAll();

$users_db = new MisfitUsers();
$users = $users_db->getAllByGroup();

foreach ($exps as $exp) {
	$id_exp = $exp["id_exp"];
	$id_group = $exp["id_group"];
	$id_twitter = $exp["id_twitter"];
	
	if (empty($users[$id_group]))
		continue;
	
	Logger::log("Processing morning event of exp #$id_exp on group #$id_group of ".sizeof($users[$id_group])." members");
		
	$classname = 'MisfitExpChecker' . $id_exp;
	$checker = new $classname($id_twitter);
	$checker->checkMorningEvent($exp, $users[$id_group]);
}
Logger::log("FINISHED\n");
