<?php
set_include_path(get_include_path() . PATH_SEPARATOR . './libs');

include_once 'configs.php';
include_once 'Misfit/Users.php';
include_once 'Misfit/Twitter.php';
include_once 'Misfit/Mongo.php';
include_once 'Misfit/Message.php';

echo "STARTED: " . date("Y-m-d H:i:s") . "\n";

$users_db = new MisfitUsers();
$users_db->syncAll();

$twitter = MisfitTwitter::getInstance();
$users = $users_db->getAllByScore();
for ($i=0; ($i+1)<sizeof($users); $i++) {
	$user = $users[$i];
	if ($user['current_score'] != $user['old_score']) { // if new score is updated		
		
		$passed = getPassedUsers($users, $i);
		if (sizeof($passed) > 0) {
			$message = '';
			
			// rule #1: A takes first place
			if ($i==0) 
				$message = MisfitMessage::AfirstB($user, $passed[0]);
			
			// rule #2 A passed 3 to 5 B
			else if (sizeof($passed) >= 3)
				$message = MisfitMessage::Apassed3B($user, $passed);
			
			// rule #3: A neck B
			else if ($user['current_score'] == $users[$i+1]['current_score'])
				$message = MisfitMessage::AneckB($user, $users[$i+1]);
			
			// rule #4: A passed B
			else if ($user['current_score'] > $passed[0]['current_score'])
				$message = MisfitMessage::ApassedB($user, $passed[0]);
			
			if ($message != '') {
				echo 'Tweeting: ' . $message . "\n";
				$twitter->send($message);
			}		
		}
		
		$behinds = getBehindUsers($users, $i);
		if (sizeof($behinds) > 0) {
			// rule #5: A behind B by 30 points
			$message = MisfitMessage::AbehindB($user, $behinds[0]);
			echo 'Tweeting: ' . $message . "\n";
			$twitter->send($message);
		}
	}
}

echo "FINISHED: " . date("Y-m-d H:i:s") . "\n\n";

function getPassedUsers($users, $i) {
	$passed = array();
	$user = $users[$i];
	for ($j = $i+1; $j<sizeof($users); $j++) {
		$loser = $users[$j];
		if (($user['old_score'] <= $loser['old_score']) // if previously, $user.score <= $loser.score
				&& ($user['current_score'] > $loser['current_score']) // and now $user.score > $loser.score
		) {
			$passed[] = $loser;
			if (sizeof($passed) == 5) break; // get max 5 passed losers
		}
	}
	return $passed;
}

function getBehindUsers($users, $i) {
	$behinds = array();
	$user = $users[$i];
	for ($j = $i-1; $j>0; $j--) {
		$winner = $users[$j];
		$diff = round($winner['current_score']/2.5) - round($user['current_score']/2.5);
		if (($user['old_score'] <= $winner['old_score']) // if previously, $user.score <= $winner.score
				&& ($diff >=1 && $diff <= 30) // and now $user.score < $winner.score by 30 points
		) {
			$behinds[] = $winner;
		}
	}
	return $behinds;
}
