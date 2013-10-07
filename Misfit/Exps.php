<?php
include_once 'Misfit/DbModelAbstract.php';

class MisfitExps extends MisfitDbModelAbstract {
	public function getAll() {
		return $this->_db->fetchAll('SELECT * FROM group_exps');
	}
	
	public function updateGroupScore($exp, $points) {
		$this->_db->query("UPDATE group_exps
				SET old_score = current_score
				WHERE id_exp = '{$exp['id_exp']}'
				AND id_group = '{$exp['id_group']}'");
		
		$this->_db->query("UPDATE group_exps 
				SET current_score = '$points'
				WHERE id_exp = '{$exp['id_exp']}'
					AND id_group = '{$exp['id_group']}'");
	}
	
	public function getOne($id_exp, $id_group) {
		return $this->_db->fetchOne("SELECT * FROM group_exps WHERE id_exp=$id_exp AND id_group = $id_group");
	}
	
	public function resetGroupGoal($exp) {
		return $this->_db->query("UPDATE group_exps
				SET old_score = 0,
					current_score = 0,
					start_date = DATE_ADD(start_date, INTERVAL 7 DAY)
				WHERE id_exp = '{$exp['id_exp']}'
				AND id_group = '{$exp['id_group']}'");
	}
	
	public function getGroups($id_exp = 0) {
		$where = '';
		if ($id_exp) $where = " WHERE id_exp = $id_exp";
		$query = "
			SELECT DISTINCT id_group
			FROM group_exps
			$where
		";
			
		return $this->fetchAll($query);
	}
}