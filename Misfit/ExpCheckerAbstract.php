<?php
include_once 'Misfit/Twitter.php';
include_once 'Misfit/Message.php';

class MisitExpCheckerAbstract {
	protected $_twitter;
	
	public function __construct($handle = 'misfitphan') {
		$this->_twitter = MisfitTwitter::getInstance($handle);
	}
	
	public function checkEvent($exp, $users) {
		// check if an interesting event happens, then send tweet message
	}
	
	public function checkMorningEvent($exp, $users) {
		// every morning, check summary leaderboard and send tweet message
	}
	
	function getPassedUsers($users, $i) {
		$passed = array();
		$user = $users[$i];
		for ($j = $i+1; $j<sizeof($users); $j++) {
			$loser = $users[$j];
			if (($user['old_score'] <= $loser['old_score']) // if previously, $user.score <= $loser.score
					&& ($user['current_score'] > $loser['current_score']) // and now $user.score > $loser.score
					&& ($loser['current_score'] > 0) // and $loser.score > 0
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
}