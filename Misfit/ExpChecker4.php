<?php
include_once ('ExpCheckerAbstract.php');
class MisfitExpChecker4 extends MisitExpCheckerAbstract {
	public function checkTwitterEvent($replies) {
		$challenge_db = new MisfitChallenges();
		
		foreach ($replies as $reply) {
			if ($challenge = $this->isChallenge($reply)) {
				$id_challenge = $challenge_db->create($challenge);
				if ($id_challenge) {
					$challenge['id'] = $id_challenge;
					$this->_twitter->send(MisfitMessage::initChallenge($challenge));
				}
			}
			elseif ($timer = $this->isTimer($reply)) {
				$challenge_db->updateTimer($timer);
				$i = $timer['i'];
				if (time() - strtotime($timer["start$i"]) <= 60) {
					$this->_twitter->send(MisfitMessage::startUser($timer, $i));
				}
			}
		}
	}
	
	public function isTimer($reply) {
		$text = $reply->text;
		$tokens = explode(" ", $text);
		
		$result = null;
		$challenge_db = new MisfitChallenges();
		// @a @b #starttimmer $id_challenge
		if ($tokens[2] == '#starttimmer' && !empty($tokens[3])) {
			$this_twitter = $reply->user->screen_name;
			$id_challenge = $tokens[3];
			$result = $challenge_db->getOneById($id_challenge);
			
			if ($this_twitter == $result['twitter1']) {
				$result['i'] = 1;
			} elseif ($this_twitter == $result['twitter2']) {
				$result['i'] = 2;
			}
			
			$result["start" . $result['i']] = date('Y-m-d H:i:s', strtotime($reply->created_at)); //convert form UTC to PST		
		}
		
		return $result;
	}
	
	public function isChallenge($reply) {
		$text = $reply->text;
		$tokens = explode(" ", $text);
		
		$result = null;
		// challenge @abc 70
		if ($tokens[1] == 'challenge') {
			$result['twitter1'] = $reply->user->screen_name;
			$result['twitter2'] = substr($tokens[2], 1); //remove the @ symbol
			$result['duration'] = !empty($tokens[3]) ? intval($tokens[3]) : 30;
			
			$result['init'] = date('Y-m-d H:i:s', strtotime($reply->created_at)); //convert form UTC to PST
		}
		
		return $result;
	}
	
	public function remindDueUsers() {
		$challenge_db = new MisfitChallenges();
		for ($i=1; $i<=2; $i++) {
			$j = 3 - $i;
			$challenges = $challenge_db->getDueUsers($i);
			if (empty($challenges)) continue;
			
			foreach ($challenges as $challenge) {
				if ($challenge["points$j"] == null) {
					$this->_twitter->send(MisfitMessage::remindUser1($challenge, $i));
				} else {
					$this->_twitter->send(MisfitMessage::remindUser2($challenge, $i));
				}
				
				$challenge_db->updateRemind($challenge, $i);
			}
		}
	}
	
	public function checkEvent($exp, $users) {
		$challenge_db = new MisfitChallenges();
		for ($k=0; ($k+1)<sizeof($users); $k++) {
			$user = $users[$k];
			if ($user['current_score'] > $user['old_score']) { // if new score is updated
				//check if this user has any challenge to be sync
				$challenge = $challenge_db->getToBeSync($user);
				
				if (!empty($challenge)) {
					if ($user['id'] == $challenge['id_user1']) {
						$i = 1;
					} elseif ($user['id'] == $challenge['id_user2']) {
						$i = 2;
					} else { // actually this won't happen
						continue; 
					}
					
					$points = $challenge_db->getPeriodPoints($challenge, $user, $i);
					Logger::log("Challenge {$challenge['id']}: User {$user['id_twitter']} just synced with {$user['current_score']} points, period points = $points");
					
					$challenge_db->updatePoints($challenge, $points, $i);
					$challenge = $challenge_db->getOne($challenge['id']); // get the updated challenge
					
					$j = 3 - $i;
					
					if ($challenge["start$j"] == null || $challenge["start$j"] == '0000-00-00 00:00:00') {
						$this->_twitter->send(MisfitMessage::remindUserToStart($challenge, $i, $j));
					} elseif ($challenge["points$j"] == null) {
						$this->_twitter->send(MisfitMessage::remindUserToSync($challenge, $i, $j));
					} else {
						$this->_twitter->send(MisfitMessage::announceResult($challenge, $i, $j));
					}					
				}
			}
		}
	}
}