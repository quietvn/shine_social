<?php
class DbManager {
	
	private static $_instances = array();
	
	private $_namespace;
	private $_link;	
	
	public static function getInstance($namespace = 'main') {
		if (!array_key_exists($namespace, self::$_instances)) {
			self::$_instances[$namespace] = new self($namespace);
		}
		return self::$_instances[$namespace];
	}
	
	private function  __construct($namespace) {
		global $DB_CONFIGS;
		$this->_namespace = $namespace;
		$config = $DB_CONFIGS[$namespace];
		$this->_link = mysql_connect($config['host'], $config['user'], $config['pass']);
		mysql_select_db($config['database'], $this->_link);
	}
	
	public function query($query) {
		return mysql_query($query, $this->_link);
	}
	
	public function fetchAll($query) {
		$result = array();
		$resultset = $this->query($query);
		while ($row = @mysql_fetch_assoc($resultset)) {
			$result[] = $row;
		}
		return $result;
	}
	
	public function fetchOne($query) {
		$resultset = $this->query($query);
		if ($row = @mysql_fetch_assoc($resultset)) {
			return $row;
		}
		return null;
	}
}