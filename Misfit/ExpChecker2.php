<?php
include_once ('ExpCheckerAbstract.php');
class MisfitExpChecker2 extends MisitExpCheckerAbstract {
	public function checkEvent($exp, $users) {
		$today = strtotime(date('Y-m-d'));
		$last_week = $today - 7*24*3600;
		$user_db = new MisfitUsers();
		
		//this will update last 7 day goals
		$total_scores = $user_db->getTotalScoreSince($exp, $users, date('Y-m-d', $last_week));
		
		for ($i=0; ($i+1)<sizeof($users); $i++) {
			$user = $users[$i];
			if ($user['current_score'] > $user['old_score'] // if new score is updated
					&& round($user['current_score'] / 2.5) >= 50) { // and new score is >= 50 
		
				$passed = $this->getPassedUsers($users, $i);
				if (sizeof($passed) > 0) {
					$message = '';
						
					// rule #1: A takes first place
					if ($i==0)
						$message = MisfitMessage::AfirstB($user, $passed[0]);
						
					// rule #2 A passed 3 to 5 B
//					else if (sizeof($passed) >= 3)
//						$message = MisfitMessage::Apassed3B($user, $passed);
						
					// rule #3: A neck B
					else if ($user['current_score'] == $users[$i+1]['current_score'])
						$message = MisfitMessage::AneckB($user, $users[$i+1]);
						
					// rule #4: A passed B by less than 50 points
					else if (round($user['current_score']/2.5) - round($passed[0]['current_score']/2.5) <= 50)
						$message = MisfitMessage::AcreepedPassedB($user, $passed[0]);
						
					// rule #5: A passed B by more than 50 points
					else if (round($user['current_score']/2.5) - round($passed[0]['current_score']/2.5) > 50)
						$message = MisfitMessage::ApassedB($user, $passed[0]);
						
					if ($message != '') {
						$this->_twitter->send($message);
					}
				}
		
				$behinds = $this->getBehindUsers($users, $i);
				if (sizeof($behinds) > 0) {
					// rule #6: A behind B by 30 points
					$message = MisfitMessage::AbehindB($user, $behinds[0]);
					$this->_twitter->send($message);
				}
			}
		}
		
		$leaderboard_db = new MisfitLeaderboard();
		$leaderboard_db->updateLeaderboard($exp, $users);
	}
	
	public function checkMorningEvent($exp, $users) {
		$leaderboard_db = new MisfitLeaderboard();
		$leaderboard = $leaderboard_db->getYesterdayLeaderboard($exp);

		$last_position = min(8, sizeof($leaderboard)); // not counting users having rank > 8
		for ($i=$last_position - 1; $i>=0; $i--) {
			$user = $leaderboard[$i];
			if ($user['points'] == 0)
				continue; // not counting users having 0 points
			
			$message = MisfitMessage::summary2($i+1, $user, $exp['id_twitter']);
			$this->_twitter->send($message);
		}
	}
}