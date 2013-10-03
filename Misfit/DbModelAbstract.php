<?php
include_once 'Db/Manager.php';
class MisfitDbModelAbstract {
	private $_db;
	
	public function __construct() {
		$this->_db = DbManager::getInstance();
	}
	
	public function fetchAll($sql) {
		return $this->_db->fetchAll($sql);
	}
	
	public function fetchOne($sql) {
		return $this->_db->fetchAll($sql);
	}
	
	public function query($sql) {
		return $this->_db->query($sql);
	}
}