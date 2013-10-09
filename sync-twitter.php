<?php
set_include_path(get_include_path() . PATH_SEPARATOR . './libs');

include_once 'configs.php';
include_once 'Logger.php';
include_once 'Misfit/Mongo.php';
include_once 'Misfit/Users.php';
include_once 'Misfit/Exps.php';
include_once 'Misfit/ExpChecker4.php';
include_once 'Misfit/Challenges.php';

Logger::log("STARTED");

$exps = new MisfitExps();
$groups = $exps->getExpGroups(4);
foreach ($groups as $group) {
	$twitter = MisfitTwitter::getInstance($group['id_twitter']);
	$replies = $twitter->load(MisfitTwitter::REPLIES);
	Logger::log("@{$group['id_twitter']}: Got " . sizeof($replies) . ' replied tweets');
	
	$checker = new MisfitExpChecker4($group['id_twitter']);
	$checker->checkTwitterEvent($replies);
	$checker->remindDueUsers();
}

Logger::log("FINISHED\n");
