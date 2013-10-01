<?php
include_once 'Db/Manager.php';
class MisfitExps {
	private $_db;
	
	public function __construct() {
		$this->_db = DbManager::getInstance();
	}
	
	public function getAll() {
		return $this->_db->fetchAll('SELECT * FROM group_exps');
	}
}