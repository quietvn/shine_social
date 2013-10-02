<?php
include_once ('ExpCheckerAbstract.php');
class MisfitExpChecker3 extends MisitExpCheckerAbstract {
	public function checkEvent($exp, $users) {
		$user_db = new MisfitUsers();
		$total_scores = $user_db->getTotalScoreSince($users, $exp['start_date']);
		
		$exp_db = new MisfitExps();
		$exp_db->updateGroupScore($exp, $total_scores['total']);
		
		$user_points = $total_scores['users'];
		
		// get the newly updated $exp
		$exp = $exp_db->getOne($exp['id_exp'], $exp['id_group']);
				
		$goal = $exp['goal'];
		$start_date = $exp['start_date'];
		
		$synced_users = $this->getSyncedUsers($users);
		
		$total_old_percent = 100 * ($exp['old_score'] / $goal);
		$total_percent = 100 * ($exp['current_score'] / $goal);
		
		$total_old_percent_floor = floor($total_old_percent/10);
		$total_percent_floor = floor($total_percent/10);
		
		Logger::log("Group {$exp['id_group']} - Old points = {$exp['old_score']} | $total_old_percent%");
		Logger::log("Group {$exp['id_group']} - New points = {$exp['current_score']} | $total_percent%");
				
		// events related to new synced user
		if (!empty($synced_users)) {
			$synced_user = $synced_users[0];
			Logger::log("Group {$exp['id_group']} - New synced user = {$synced_user['email']}");
			
			if ($total_percent_floor > $total_old_percent_floor) {
				// Rule #6: if a member syncs and causes the combined points total to be >=100% of the goal
				if ($total_percent_floor >= 10 && $total_old_percent_floor < 10) { // don't send tweet again when it reach 110%, 120% ..					
					$this->_twitter->send(MisfitMessage::AmetGoal($synced_user));
				} else { 
				//Rule #2: if a member syncs and pushes the combined points total above 10%, 20%, 30%..
					$this->_twitter->send(MisfitMessage::ApassedProgress($synced_user, $total_percent_floor));
				}
			}
		}
		
		// daily & weekly events
		$now = time();
		$diff = $now - strtotime($exp['start_date']);
		
		Logger::log("Group has passed " .floor($diff/3600/24). " days ".round(($diff/3600) % 24)." hours");
		// Rule #5: This tweet should occur exactly 7 days after the start of the week.
		if ($diff >= 7*24*3600) {
			$this->_twitter->send(MisfitMessage::wrapUp($exp, $total_percent, $total_scores['highest_user'], $total_scores['weakest_user']));
			
			$exp_db = new MisfitExps();
			$exp_db->resetGroupGoal($exp);
			
		// Rule #1: Week initiation tweet.  Should be at Monday 9AM PST.
			$this->_twitter->send(MisfitMessage::initGroup($exp));
		}
	}
	
	public function getSyncedUsers($users) {
		$synced_users = array();
		foreach ($users as $user) {
			if ($user['current_score'] > 0 && $user['current_score'] != $user['old_score']) {
				$synced_users[] = $user;
			}
		}
		return $synced_users;
	}
	
	public function getDateFromDateTime($datetime) {
		list($date,$time) = explode(" ", $datetime);
		return $date;
	}
}