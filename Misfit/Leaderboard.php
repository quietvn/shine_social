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
				ORDER BY id_group, points DESC
			";
		
		return $this->fetchAll($query);
	}
	
	public function getLeaderboard2($id_exp, $id_group = 0) {
		$where = '';
		if ($id_exp) $where .= " AND leaderboard.id_exp = $id_exp";
		if ($id_group) $where .= " AND leaderboard.id_group = $id_group";
		
		$last_week = date("Y-m-d", time() - 7*24*3600);
		$query = "
			SELECT *, users.id_twitter as user_twitter FROM leaderboard
			INNER JOIN users 
				ON users.id = leaderboard.id_user
			INNER JOIN group_exps
				ON group_exps.id_exp = leaderboard.id_exp
				AND group_exps.id_group = leaderboard.id_group
			WHERE last_date >= '$last_week'
			$where
			ORDER BY id_group, users.id, points DESC
		";
		$users = $this->fetchAll($query);
		
		$result = array();
		foreach ($users as $user) {
			$result[ $user['id_group'] ] [ $user['id_user'] ] ['start_date'] = $user['start_date'];
			$result[ $user['id_group'] ] [ $user['id_user'] ] ['goal'] = $user['goal'];
			$result[ $user['id_group'] ] [ $user['id_user'] ] ['id_group'] = $user['id_group'];
			$result[ $user['id_group'] ] [ $user['id_user'] ] ['id_twitter'] = $user['user_twitter'];
			$result[ $user['id_group'] ] [ $user['id_user'] ] ['email'] = $user['email'];
			$result[ $user['id_group'] ] [ $user['id_user'] ] ['daily_points'] [$user['last_date']] = $user['points'];
			$result[ $user['id_group'] ] [ $user['id_user'] ] ['weekly_points'] [$user['last_date']] = $user['weekly_points'];
		}
		return $result;
 	}
}
