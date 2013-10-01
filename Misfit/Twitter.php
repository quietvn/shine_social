<?php
include_once 'twitter-php/src/twitter.class.php';

class MisfitTwitter extends Twitter{

	private static $_instances = array();
	
	private $twitter;

	public static function getInstance($handle) {
		if (!in_array($handle, self::$_instances)) {
			self::$_instances[$handle] = new self($handle);
		}
		return self::$_instances[$handle];
	}

	public function  __construct($handle) {
		global $TWITTER_CONFIGS;
		$configs = $TWITTER_CONFIGS[$handle];
		parent::__construct($configs['consumerKey']
				,$configs['consumerSecret']
				,$configs['accessToken']
				,$configs['accessTokenSecret']);
	}
	
	public function send($message) {
		try {
			parent::send($message);			
		} catch (Exception $e) {
			Logger::log("TWITTER EXEPTION :: " . $e);
		}
	}
}