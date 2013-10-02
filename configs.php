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
	'misfitphan' => array(
		'consumerKey' => 'sSTPGBSB7H7iGVknkDYQ',
		'consumerSecret' => 'QOfc5TfOaG7VUgKezHudhP09ENRu1CUnl6RGlPA5us',
		'accessToken' => '1908282224-ooVamTjYHffy8GTsnJTdcwWpT6kWOwmPQl3tS83',
		'accessTokenSecret' => '2ViGTiwCjel3gIHU5Mk2aL2IiJXTRpAzFGMv2RFYvE'
	),
	'shinelabs2' => array(
			'consumerKey' => 'PkrCTjsBysU3RxA3C4a3bA',
			'consumerSecret' => '2rmDkO50UsCYjflwgYTtNcMAM0J76XvTyEZvxlp0',
			'accessToken' => '1921579628-0BvXrPs8c9rPTHDkX1ejT8MLiqAaJi23DBD987I',
			'accessTokenSecret' => 'tR1Eg1LhcQ4iYw0WP00j0W9pwjUcSi93kCwm9g2ww'
	),
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

define("TIMEZONE", "US/Pacific");
define("TIMEZONE_OFFSET", "-07:00");

date_default_timezone_set(TIMEZONE);