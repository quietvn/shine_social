<?php
include_once 'Misfit/DbModelAbstract.php';
class MisfitLeaderboard extends MisfitDbModelAbstract {
	
	public function updateLeaderboard($exp, $users, $point_column = 'current_score', $point_system = 'system') {
		Logger::log("Updating Leaderboard for group {$exp['id_group']} - exp {$exp['id_exp']}");
		$date = date("Y-m-d");
		foreach ($users as $user) {
			
			if ($point_system == 'system') {
				$points = round($user[$point_column] / 2.5);
			} else {
				$points = $user[$point_column];
			}
			
			$this->query("
				INSERT INTO leaderboard (id_exp, id_group, last_date, id_user, points)
				VALUES ({$exp['id_exp']}, {$exp['id_group']}, '$date', {$user['id']}, {$points})
				ON DUPLICATE KEY
					UPDATE points = {$user[$point_column]}
			");
		}
	}
	
	public function getYesterdayLeaderboard($exp) {
		$yesterday = date("Y-m-d", time() - 24*3600);
		
		$query = "
				SELECT * FROM leaderboard
				INNER JOIN users
					ON leaderboard.id_user = users.id 
				WHERE last_date = '$yesterday'
					AND id_exp = {$exp['id_exp']}
					AND id_group = {$exp['id_group']}
				ORDER BY points DESC
			";
		
		return $this->fetchAll($query);
	}
}
