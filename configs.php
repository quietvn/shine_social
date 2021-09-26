<?php
$DB_CONFIGS = array(
	'main' => array (
		'host' => 'localhost',
		'user' => 'root',
		'pass' => '',
		'database' => 'misfit'
	)
);
global $DB_CONFIGS;

$TWITTER_CONFIGS = array(
	'consumerKey' => 'xxx',
	'consumerSecret' => 'xxx',
	'accessToken' => 'xxx',
	'accessTokenSecret' => 'xxx'
);
global $TWITTER_CONFIGS;

$MONGO_CONFIG = array(
	1 => array(
		'name' => 'Production',
		'url' => 'mongodb://localhost',
		'collection' => 'shine'),
	2 => array(
		'name' => 'Staging',
		'url' => 'mongodb://localhost',
		'collection' => 'shine')	
);
global $MONGO_CONFIG;
