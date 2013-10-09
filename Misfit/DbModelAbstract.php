<?php
include_once 'Db/Manager.php';
class MisfitDbModelAbstract {
	protected $_db;
	
	public function __construct() {
		$this->_db = DbManager::getInstance();
	}
	
	public function fetchAll($sql) {
		return $this->_db->fetchAll($sql);
	}
	
	public function fetchOne($sql) {
		return $this->_db->fetchOne($sql);
	}
	
	public function query($sql) {
		return $this->_db->query($sql);
	}
	
	public function getInsertedId() {
		return $this->_db->getInsertedId();
	}
}