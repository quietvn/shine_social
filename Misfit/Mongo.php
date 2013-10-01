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
			self::$_instances[$server]->collection = self::$_instances[$server]->$MONGO_CONFIG[$server]['collection'];
		}
		return self::$_instances[$server];
	}
}