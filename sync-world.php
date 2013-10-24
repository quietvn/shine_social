<?php
set_include_path(get_include_path() . PATH_SEPARATOR . './libs');

include_once 'configs.php';
include_once 'libs/Cache.php';
include_once 'libs/Logger.php';
include_once 'Misfit/Twitter.php';
include_once 'Misfit/Timeline.php';
include_once 'Misfit/Mongo.php';


$last_time = Cache::get("last_timeline");

Logger::log('STARTED');
$result = MisfitTimeline::getLatestTimeline($last_time);
$size = sizeof($result);
Logger::log("Found {$size} new achievements since $last_time | ".date("Y-m-d H:i:s", $last_time)."!");
if (!empty($result)) {
	$items = $result['items'];
	$users = $result['users'];
	$goals = $result['goals'];

	$twitter = MisfitTwitter::getInstance('world');
	for ($i=sizeof($items)-1; $i>=0; $i--) {
		$item = $items[$i];
		$user = $users[$item['uid']->{'$id'}];
		$goal = $goals[$item['uid']->{'$id'}];
		Cache::save("last_timeline", $item['ts']);
		
		$message = MisfitTimeline::getMessage($item, $user, $goal);
		if (!empty($message)) {
			try {
				$twitter->send($message);
			} catch (Exception $e) {
				// ignore
			}
			sleep(10);
		}
	}
}
Logger::log('FINISHED');