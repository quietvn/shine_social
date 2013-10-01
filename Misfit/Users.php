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
		return $this->_db->fetchAll('SELECT * FROM users ORDER BY current_score DESC');
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
	}
	
	public function syncCurrentScoresOld() {
		$shine_ids = $this->getShineIds();
		$mongo = MisfitMongo::getInstance()->collection;
				
		foreach ($shine_ids as $shine_id) {
			$query = array( "uid" => $shine_id);		
			$goal = $mongo->goals->find($query)->sort(array('st' => -1))->limit(1);
			if ($goal->hasNext()) {
				$goal = $goal->getNext();
				$this->updateScore($shine_id, $goal);
			}
		}
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
				$query = "INSERT INTO users(id_server,id_shine,email,id_group,id_twitter)
					VALUES ('".$data['id_server']."','".$shine_user['_id']->{'$id'}."', '".$data['email']."', '".$data['group']."', '".$data['id_twitter']."')";
				return $this->_db->query($query);					
			} else {
				echo "Cannot find Shine user!";
			}
		}
	}
	
	public function findOne($data) {
		return $this->_db->fetchOne("SELECT * FROM users WHERE id_server='".$data['id_server']."' AND email='".$data['email']."'");
	}
	
	public function delete($id) {
		return $this->_db->query("DELETE FROM users WHERE id=" . $id);
	}
}