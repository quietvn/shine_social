<?php
include_once 'Misfit/DbModelAbstract.php';
class MisfitChallenges extends MisfitDbModelAbstract {
	const RESCUE_DELAY = 2; // 2 minutes delay for Rescue jobs to be finished
	
	public function create($c) {
		$user_db = new MisfitUsers();
		$user1 = $user_db->findOneByTwitter($c['twitter1']);
		$user2 = $user_db->findOneByTwitter($c['twitter2']);
		
		$return = null;
		if ($user1 && $user2) {
			$existing = $this->findExisting($user1['id'], $user2['id'], $c['init']);
			if (!$existing) {
				$query = "INSERT IGNORE INTO challenges (twitter1, twitter2, id_user1, id_user2, init, duration)
					VALUES ('{$c['twitter1']}', '{$c['twitter2']}', {$user1['id']}, {$user2['id']}, '{$c['init']}', '{$c['duration']}')";
				$this->query($query);
				$return = $this->getInsertedId();
			} else {
				Logger::log("Existed challenge {$user1['id']}, {$user2['id']}, {$c['init']}");
			}
		}
		
		return $return;
	}
	
	public function findExisting($id_user1, $id_user2, $init) {
		return $this->fetchOne("SELECT * FROM challenges 
			WHERE id_user1=$id_user1
				AND id_user2=$id_user2
				AND init='$init'");
	}
	
	public function getOneById($id) {
		return $this->fetchOne("SELECT * FROM challenges WHERE id=$id");
	}
	
	public function updateTimer($result) {
		Logger::log("Challenge {$result['id']}: Updated start1={$result['start1']}, start2={$result['start2']}");
		return $this->query("UPDATE challenges SET start1='{$result['start1']}', start2='{$result['start2']}' WHERE id={$result['id']}");
	}
	
	public function getDueUsers($i) {
		$query = "
			SELECT * FROM challenges
			WHERE (TO_SECONDS(NOW()) - TO_SECONDS(start$i) >= duration*60 && remind$i IS NULL)
		";
		
		return $this->fetchAll($query);
	}
	
	public function updateRemind($challenge, $i) {
		$query = "
			UPDATE challenges
			SET remind$i = NOW()
			WHERE id = {$challenge['id']}
		";
		
		return $this->query($query);
	}
	
	public function getToBeSync($user) {
		$query = "
			SELECT * FROM challenges
			WHERE (id_user1 = {$user['id']} && remind1 IS NOT NULL && points1 IS NULL)
				OR (id_user2 = {$user['id']} && remind2 IS NOT NULL && points2 IS NULL)
		";
		
		return $this->fetchOne($query);
	}
	
	public function updatePoints($challenge, $points, $i) {
		$query = "
			UPDATE challenges
			SET points$i = $points
			WHERE id = {$challenge['id']}
		";
		
		return $this->query($query);
	}
	
	public function getOne($id) {
		return $this->fetchOne("SELECT * FROM challenges WHERE id = $id");
	}
	
	public function getPeriodPoints($challenge, $i) {
		$mongo = MisfitMongo::getInstance($challenge['id_server'])->collection_raw;
		$uid = new MongoId($challenge['id_shine']);
		$query = array( "uid" => $uid,
				"et" => array('$gt' => strtotime($challenge["start$i"]),
							'$lte' => strtotime($challenge["remind$i"]))
		);
		$activities = $mongo->activities->find($query);
		$points = 0;
		while ($activities->hasNext()) {
			$activitiy = $activities->getNext();
			if (!empty($activitiy['pt']))
				$points += $activitiy['pt'];
		}
				
		return round($points / 2.5);
	}
	
	public function updateSync($challenge, $i) {
		$query = "
			UPDATE challenges
			SET sync$i = '".date("Y-m-d H:i:s")."'
			WHERE id = {$challenge['id']}
		";
		
		return $this->query($query);
	}
	
	public function getSyncedUsers($i) {
	 	$query = "
			SELECT challenges.*, id_shine, id_server, id_twitter FROM challenges
			INNER JOIN users
				ON users.id = id_user$i
			WHERE (TO_SECONDS(NOW()) - TO_SECONDS(sync$i) >= ".self::RESCUE_DELAY."*60 && points$i IS NULL)
		";
		
		return $this->fetchAll($query);
	}
}