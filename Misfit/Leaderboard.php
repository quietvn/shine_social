<?php
include_once 'Misfit/DbModelAbstract.php';
class MisfitLeaderboard extends MisfitDbModelAbstract {
	
	public function updateLeaderboard($exp, $users, $point_column = 'current_score', $point_system = 'system') {
		Logger::log("Updating Leaderboard for group {$exp['id_group']} - exp {$exp['id_exp']}");
		$date = date("Y-m-d");
		foreach ($users as $user) {			
			$points = round($user['current_score'] / 2.5);			
			$weekly_points = isset($user['total_points']) ? $user['total_points'] : 0;
			
			$this->query("
				INSERT INTO leaderboard (id_exp, id_group, last_date, id_user, points, weekly_points)
				VALUES ({$exp['id_exp']}, {$exp['id_group']}, '$date', {$user['id']}, {$points}, {$weekly_points})
				ON DUPLICATE KEY
					UPDATE points = {$points},
						weekly_points = {$weekly_points}
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
