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
	'consumerKey' => 'sSTPGBSB7H7iGVknkDYQ',
	'consumerSecret' => 'QOfc5TfOaG7VUgKezHudhP09ENRu1CUnl6RGlPA5us',
	'accessToken' => '1908282224-ooVamTjYHffy8GTsnJTdcwWpT6kWOwmPQl3tS83',
	'accessTokenSecret' => '2ViGTiwCjel3gIHU5Mk2aL2IiJXTRpAzFGMv2RFYvE'
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