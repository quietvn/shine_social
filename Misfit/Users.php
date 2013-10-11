<?php
include_once 'Misfit/DbModelAbstract.php';
class MisfitUsers extends MisfitDbModelAbstract {
	const REPORT_BEFORE_DAYS = 14;
	
	public function getAll() {
		return $this->_db->fetchAll('SELECT * FROM users');
	}
	
	public function getAllByScore($id_group = '') {
		$where = '';
		if (!empty($id_group))
			$where = "WHERE id_group=$id_group";
		
		return $this->_db->fetchAll("SELECT users.*, 
					GROUP_CONCAT(group_users.id_group ORDER BY group_users.id_group SEPARATOR ',') groups 
				FROM users
				LEFT JOIN group_users ON users.id = group_users.id_user
				$where 
				GROUP BY users.id
				ORDER BY current_score DESC");
	}
	
	public function syncAll() {
		$this->updateOldScores();
		$this->syncCurrentScores();
	}
	
	public function updateOldScores() {
		return $this->_db->fetchAll('UPDATE users SET old_score = current_score');
	}
	
	public function syncCurrentScores() {
		$users = $this->getAll();		
		Logger::log("=== UPDATING SCORE ===");	
		foreach ($users as $user) {
			$shine_id = new MongoId($user['id_shine']);
			$mongo = MisfitMongo::getInstance($user['id_server'])->collection;
			
			$query = array( "uid" => $shine_id, 
					"et" => array('$gt' => time())
			);
			$goal = $mongo->goals->find($query)->sort(array('st' => -1))->limit(1);
			$points = 0;
			if ($goal->hasNext()) {
				$goal = $goal->getNext();
				if (!empty($goal['prgd']['points']))
					$points = $goal['prgd']['points'];				
			}
			
			$this->updateScore($user, $points);
			
		}
		Logger::log("=== UPDATING SCORE ===");
	}
	
	public function updateScore($user, $points) {
		
		$new = '';
		if ($points != $user['old_score']) {
			$new = " (NEW)";
		}
		
		global $MONGO_CONFIG;
		
		Logger::log("UPDATING ".$user['email']." [".$MONGO_CONFIG[ $user['id_server'] ]['name']."] TO ".$points." | " . round($points/2.5) . $new);
		return $this->_db->query("UPDATE users SET current_score = ".$points." WHERE id_shine = '".$user['id_shine']."'");
	}
	
	public function getShineIds() {
		$ids = $this->_db->fetchAll('SELECT id_shine FROM users');
		$result = array();
		foreach ($ids as $id) {
			$result[] = new MongoId($id['id_shine']);
		}
		return $result;
	}
	
	public function addNewUser($data) {
		$result = array();
		
		if (empty($data['id_twitter'])) {
			$result['flash'][] = "Twitter handle cannot be empty!";
			return $result;
		}
		
		$existingUser = $this->findOne($data);		
		if (!empty($existingUser)) {
			$this->_db->query("UPDATE users SET id_twitter = '{$data['id_twitter']}' WHERE id={$existingUser['id']}");
			$this->addNewUserGroups($existingUser['id'], $data['groups']);
			$result['flash'][] = "Updated info for user {$data['email']}!";
		} else {
			$mongo = MisfitMongo::getInstance($data['id_server'])->collection;
			$shine_user = $mongo->users->findOne(array('email' => $data['email']));
			if (!empty($shine_user)) {
				$query = "INSERT INTO users(id_server,id_shine,email,id_twitter, start_date)
					VALUES ('".$data['id_server']."','".$shine_user['_id']->{'$id'}."', '".$data['email']."', '".$data['id_twitter']."'
						, '".date("Y-m-d")."')";
				$result = $this->_db->query($query);
				$id_user = $this->_db->getInsertedId();
				$this->addNewUserGroups($id_user, $data['groups']);	
				$result['flash'][] = "Inserted new user having email ".$data['email']."!";
			} else {
				$result['flash'][] = "Cannot find Shine user!";
			}
		}
		
		return $result;
	}
	
	public function addNewUserGroups($id_user, $groups) {
		$this->_db->query("DELETE FROM group_users WHERE id_user=$id_user");
		$id_groups = !empty($groups) ? explode(',', $groups) : array();		
		
		if (!empty($id_groups)) {
			foreach ($id_groups as $id_group) {
				if (!empty($id_group))
					$this->_db->query("INSERT INTO group_users (id_user, id_group)
						VALUES ($id_user, '$id_group')");
			}
		}
	}
	
	public function findOne($data) {
		return $this->_db->fetchOne("SELECT * FROM users WHERE id_server='".$data['id_server']."' AND email='".$data['email']."'");
	}
	
	public function delete($id) {
		return $this->_db->query("DELETE FROM users WHERE id=" . $id);
	}
	
	public function getAllByGroup() {
		$users = $this->_db->fetchAll("SELECT group_users.id_group, users.* FROM users
				INNER JOIN group_users ON users.id = group_users.id_user
				ORDER BY current_score DESC");
		$result = array();
		foreach ($users as $user) {
			$result[ $user['id_group'] ] [] = $user;
		}
		
		return $result;
	}
	
	public function getTotalScoreSince($exp, $users, $start_time) {
		$result = array();
		$total_points = 0;		
		$user_points = array();
		$highest_user = array("total_points" => -1);
		$weakest_user = array("total_points" => PHP_INT_MAX);
		
		foreach ($users as $user) {		
			$mongo = MisfitMongo::getInstance($user['id_server'])->collection;
			$uid = new MongoId($user['id_shine']);
			$query = array( "uid" => $uid,
					"et" => array('$gt' => strtotime($start_time))
			);
			$goals = $mongo->goals->find($query)->sort(array('st' => 1));
			$points = 0;
			while ($goals->hasNext()) {
				$goal = $goals->getNext();
				if (!empty($goal['prgd']['points']))
					$points += round($goal['prgd']['points'] / 2.5);
				$this->updateLeaderboard($exp, $user, $goal, $points);	
			}
			
			$total_points += $points;
			$user['total_points'] = $points;
			$user_points[ $user['id'] ] = $user;
			
			if($user['total_points'] > $highest_user['total_points'])
				$highest_user = $user;
			
			if($user['total_points'] < $weakest_user['total_points'])
				$weakest_user = $user;
			
			Logger::log("User {$user['email']} has total {$points} points.");
		}
		Logger::log("This group has total {$total_points} points.");
		
		$result['total'] = $total_points;
		$result['users'] = $user_points;
		$result['highest_user'] = $highest_user;
		$result['weakest_user'] = $weakest_user;
		return $result;
	}
	
	public function updateLeaderboard($exp, $user, $goal, $weekly_points) {
		$daily_points = !empty($goal['prgd']['points']) ? round($goal['prgd']['points'] / 2.5) : 0;			
		$weekly_points = $weekly_points;
		$date = date('Y-m-d', $goal['et']);
		
		Logger::log("User {$user['email']} - $date: daily points = $daily_points, weekly points = $weekly_points");
		
		return $this->query("
			INSERT INTO leaderboard (id_exp, id_group, last_date, id_user, points, weekly_points)
			VALUES ({$exp['id_exp']}, {$exp['id_group']}, '$date', {$user['id']}, {$daily_points}, {$weekly_points})
			ON DUPLICATE KEY
				UPDATE points = {$daily_points},
					weekly_points = {$weekly_points}
		");
	}
	
	public function findOneByTwitter($twitter) {
		return $this->fetchOne("SELECT * FROM users WHERE id_twitter = '{$twitter}'");	
	}
	
	public function getGroups() {
		$query = "
			SELECT DISTINCT id_group
			FROM group_users
		";
			
		return $this->fetchAll($query);
	}
	
	public function getAllByIdGroup($id_group) {
		$sql = "
			SELECT group_users.id_group, users.* 
			FROM users
			INNER JOIN group_users ON users.id = group_users.id_user
			WHERE id_group = '$id_group'";
		$users = $this->_db->fetchAll($sql);
		$result = array();
		foreach ($users as $user) {
			$result[ $user['id_shine'] ] = $user;
		}
		
		return $result;
	}
	
	public function getAvgPointsBefore($user, $start, $end) {
		return $this->getAvgPoints($user, strtotime($start) - self::REPORT_BEFORE_DAYS*24*3600, strtotime($start));
	}
	
	public function getAvgPointsAfter($user, $start, $end) {
		return $this->getAvgPoints($user, strtotime($start), strtotime($end));
	}
	
	public function getAvgPoints($user, $start, $end) {
		$mongo = MisfitMongo::getInstance($user['id_server'])->collection;
		$uid = new MongoId($user['id_shine']);
		$query = array( 
					"uid" => $uid,
					"et" => array(
						'$gt' => $start,
						'$lte' => $end + 24*3600
					)
				);
//print_r($query);
		$goals = $mongo->goals->find($query);
		$points = 0;
		$i = 0;
		while ($goals->hasNext()) {
			$goal = $goals->getNext();
			$i++;
			if (!empty($goal['prgd']['points']))
				$points += round($goal['prgd']['points'] / 2.5);
		}
		
		return ($i==0)?0:round($points / $i);
	}
	
	public function getAvgSyncBefore($user, $start, $end) {
		return $this->getAvgSync($user, strtotime($start) - self::REPORT_BEFORE_DAYS*24*3600, strtotime($start) - 24*3600);
	}
	
	public function getAvgSyncAfter($user, $start, $end) {
		return $this->getAvgSync($user, strtotime($start), strtotime($end));
	}
	
	public function getAvgSync($user, $start, $end) {
		$duration = 1 + ($end - $start)/(24*3600);
		$mongo = MisfitMongo::getInstance($user['id_server'])->collection;
		$uid = new MongoId($user['id_shine']);
		$query = array( 
					"uid" => $uid,
					"et" => array(
						'$gt' => $start,
						'$lte' => $end + 24*3600
					)
				);
//print_r($query);
		$logs = $mongo->logs->find($query);
		$syncs = array(1=>0, 2=>0, 3=>0);
		
		while ($logs->hasNext()) {
			$log = $logs->getNext();
			$data = $log['data'];
			$syncMode = 1;
			if (!empty($data['syncMode']))
				$syncMode = $data['syncMode'];
			
			$syncs[$syncMode]++;
		}
				
		$result = array();
		for ($i=1; $i<=3; $i++) {
			$result[$i] = round($syncs[$i] / $duration, 2);
		}
		return $result;
	}
}
