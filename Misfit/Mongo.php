<?php

class MisfitMongo {
	private static $_instances = array();

	public static function getInstance($server = 1) {
		if (empty($server))
			$server = 1;
		
		if (!isset(self::$_instances[$server])) {			
			global $MONGO_CONFIG;
			MongoCursor::$slaveOkay = true;
			self::$_instances[$server] = new MongoClient($MONGO_CONFIG[$server]['url']);
			$db_name = $MONGO_CONFIG[$server]['collection'];
			$db_name_raw = $db_name . "_raw";
			self::$_instances[$server]->collection = self::$_instances[$server]->$db_name;
			self::$_instances[$server]->collection_raw = self::$_instances[$server]->$db_name_raw;
		}
		return self::$_instances[$server];
	}
}