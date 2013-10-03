<?php
include_once 'Db/Manager.php';
class MisfitUsers {
	private $_db;
	
	public function __construct() {
		$this->_db = DbManager::getInstance();
	}
	
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
				$query = "INSERT INTO users(id_server,id_shine,email,id_twitter)
					VALUES ('".$data['id_server']."','".$shine_user['_id']->{'$id'}."', '".$data['email']."', '".$data['id_twitter']."')
					ON DUPLICATE KEY
						UPDATE users SET id_twitter = '".$data['id_twitter']."'";
				$this->_db->query($query);
				$id_user = $this->_db->getInsertedId();
				$this->addNewUserGroups($id_user, $data['groups']);			
				$result['flash'][] = "Inserted new user having email {$data['email']}!";
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
				$this->_db->query("INSERT INTO group_users (id_user, id_group)
					VALUES ($id_user, $id_group)");
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
	
	public function getTotalScoreSince($users, $start_time) {
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
			$goals = $mongo->goals->find($query)->sort(array('st' => -1))->limit(1);
			$points = 0;
			while ($goals->hasNext()) {
				$goal = $goals->getNext();
				if (!empty($goal['prgd']['points']))
					$points += round($goal['prgd']['points'] / 2.5);
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
}