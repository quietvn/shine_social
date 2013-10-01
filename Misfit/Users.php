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
	
	public function getAllByScore() {
		return $this->_db->fetchAll("SELECT users.*, 
					GROUP_CONCAT(group_users.id_group ORDER BY group_users.id_group SEPARATOR ',') groups 
				FROM users
				LEFT JOIN group_users ON users.id = group_users.id_user 
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
		echo "=== UPDATING SCORE ===\n";	
		foreach ($users as $user) {
			$shine_id = new MongoId($user['id_shine']);
			$mongo = MisfitMongo::getInstance($user['id_server'])->collection;
			
			$query = array( "uid" => $shine_id);
			$goal = $mongo->goals->find($query)->sort(array('st' => -1))->limit(1);			
			if ($goal->hasNext()) {
				$goal = $goal->getNext();
				$this->updateScore($user, $shine_id, $goal);
			}
		}
		echo "=== UPDATING SCORE ===\n\n";
	}
	
	public function updateScore($user, $shine_id, $goal) {
		if (empty($goal['prgd']['points']))
			$goal['prgd']['points'] = 0;
		
		global $MONGO_CONFIG;
		echo "UPDATING ".$user['email']." [".$MONGO_CONFIG[ $user['id_server'] ]['name']."] TO ".$goal['prgd']['points']." \n";
		return $this->_db->query("UPDATE users SET current_score = ".$goal['prgd']['points']." WHERE id_shine = '".$shine_id."'");
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
		$existingUser = $this->findOne($data);
		
		if (!empty($existingUser)) {
			echo "Cannot add the same user in the same server!";
		} else {
			$mongo = MisfitMongo::getInstance($data['id_server'])->collection;
			$shine_user = $mongo->users->findOne(array('email' => $data['email']));
			if (!empty($shine_user)) {
				$query = "INSERT INTO users(id_server,id_shine,email,id_twitter)
					VALUES ('".$data['id_server']."','".$shine_user['_id']->{'$id'}."', '".$data['email']."', '".$data['id_twitter']."')";
				$this->_db->query($query);
				$id_user = $this->_db->getInsertedId();
				$this->addNewUserGroups($id_user, $data['groups']);					
			} else {
				echo "Cannot find Shine user!";
			}
		}
	}
	
	public function addNewUserGroups($id_user, $groups) {
		echo "Insert $id_user to group $groups";
		if (empty($groups)) {
			$id_groups = array(1);
		} else {
			$id_groups = explode(',', $groups);
		}
		
		foreach ($id_groups as $id_group) {
			$this->_db->query("INSERT INTO group_users (id_user, id_group)
				VALUES ($id_user, $id_group)");
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
}