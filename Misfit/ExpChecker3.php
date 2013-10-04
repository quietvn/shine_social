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
					$remaining_score = $goal - $exp['current_score'];
					$this->_twitter->send(MisfitMessage::ApassedProgress($synced_user, $total_percent_floor, $remaining_score, $goal));
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
		
		$leaderboard_db = new MisfitLeaderboard();
		$leaderboard_db->updateLeaderboard($exp, $total_scores['users'], 'total_points', 'user');
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
	
	public function checkMorningEvent($exp, $users) {
		$leaderboard_db = new MisfitLeaderboard();
		$leaderboard = $leaderboard_db->getYesterdayLeaderboard($exp);
		
		if (empty($leaderboard)) return;
		
		$now = time();
		$diff = $now - strtotime($exp['start_date']);
		$passed_days = floor($diff/3600/24);
		$passed_hours = round(($diff/3600) % 24);
		$expected_points = $exp['goal'] * ($passed_days / 7);
		
		Logger::log("Group goal: {$exp['goal']} points");
		Logger::log("Group has passed " .$passed_days. " days ".$passed_hours." hours");			
		Logger::log("Group expected weekly points: " . $expected_points);
		
		if ($passed_days > 0) {
			$summary = $this->getSummaryPoints($leaderboard);
			
			$total_weekly_points = $summary['total_weekly_points'];
			$total_yesterday_points = $summary['total_yesterday_points'];
			
			
			$remaining_points = $exp['goal'] - $total_weekly_points;
			$remaining_days = 7 - $passed_days;
			$remaining_daily_points = $remaining_points / $remaining_days;
			
			// Rule #3a Yesterday team did not meet goal
			if ($total_weekly_points < $expected_points) {
				$expected_percentage = round(100*($remaining_daily_points - $total_yesterday_points) / $total_yesterday_points);
				
				$message = MisfitMessage::dailyBehindGoal($expected_percentage);
				$this->_twitter->send($message);
			}
			// Rule #3a Yesterday team did meet goal
			else {				
				$message = MisfitMessage::dailyMetGoal();
				$this->_twitter->send($message);
			}
			
			// Rule #4: Daily MVP: mention best and weakest contributors
			$this->_twitter->send(MisfitMessage::dailyMVP($summary['highest_user'], $summary['weakest_user']));
		}
	}
	
	public function getSummaryPoints($leaderboard) {
		$result = array();
		$result['total_weekly_points'] = 0;
		$result['total_yesterday_points'] = 0;
		$result['highest_user'] = $leaderboard[0];
		$result['weakest_user'] = $leaderboard[0];
		
		foreach ($leaderboard as $user) {
			$result['total_weekly_points'] += $user['weekly_points'];
			$result['total_yesterday_points'] += $user['points'];
			
			if ($user['points'] > $result['highest_user']['points'])
				$result['highest_user'] = $user;
			
			if ($user['points'] < $result['weakest_user']['points'])
				$result['weakest_user'] = $user;
		}
		
		Logger::log("Total weekly points: " . $result['total_weekly_points']);
		Logger::log("Total yesterday points: " . $result['total_yesterday_points']);
		Logger::log("Best contributor: " . $result['highest_user']['id_twitter'] ." with ".$result['highest_user']['points']." points");
		Logger::log("Weakest contributor: " . $result['weakest_user']['id_twitter'] ." with ".$result['weakest_user']['points']." points");
		
		return $result;
	}
}