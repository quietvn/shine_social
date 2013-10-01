<?php
include_once 'twitter-php/src/twitter.class.php';

class MisfitTwitter extends Twitter{

	private static $_instance;
	
	private $twitter;

	public static function getInstance() {
		if (null == self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function  __construct() {
		global $TWITTER_CONFIGS;
		$configs = $TWITTER_CONFIGS;
		parent::__construct($configs['consumerKey']
				,$configs['consumerSecret']
				,$configs['accessToken']
				,$configs['accessTokenSecret']);
	}
}