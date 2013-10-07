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
	'shinelabs' => array(
		'consumerKey' => 'xEiIj7K4W95P3DXsldmYIg',
		'consumerSecret' => 'P1igXYkjB2cqdxsloO2dZbS6j99ciegit2Uhzm7MbQ',
		'accessToken' => '1908747642-8DduN9aYxoDjr4WkNq6NOueYVrPIP9HM3fNv9Wa',
		'accessTokenSecret' => 'gGfqJfSYSQg87yooFlvJ9ulLh0EeoO91Q9fiI1o7g'
	),
	'shinelabs2' => array(
			'consumerKey' => 'PkrCTjsBysU3RxA3C4a3bA',
			'consumerSecret' => '2rmDkO50UsCYjflwgYTtNcMAM0J76XvTyEZvxlp0',
			'accessToken' => '1921579628-0BvXrPs8c9rPTHDkX1ejT8MLiqAaJi23DBD987I',
			'accessTokenSecret' => 'tR1Eg1LhcQ4iYw0WP00j0W9pwjUcSi93kCwm9g2ww'
	),
	'shinelabs3' => array(
			'consumerKey' => 'bEey8wMKK8xSXBEMm3Eg',
			'consumerSecret' => 'tP2hZAvlVqEcYryfewvtiRjCyhUZbv9d7N4WC6d4Q',
			'accessToken' => '1921587698-TMpASIvlg25hPCuQX6w3jCMGQ5P5RGdFce4C2tj',
			'accessTokenSecret' => '8Tdui1koKeM1a1XVFysuU8CpexJTlaqj87vq9ZovCI'
	),
	'shinelabs4' => array(
			'consumerKey' => 'OegudIUF6A4KztRzZz6Q',
			'consumerSecret' => 'ld3F9AthJQEQIlM8ouzAydeqr6ocPwBza3pR43RDhA',
			'accessToken' => '1924251631-0HtZ6WOeJSCsoeYpjLpASWwcWcp2Ysvdgo8ZVF1',
			'accessTokenSecret' => 'GcXDN9aUiPLGbxV8zurAZTua4cIwnaVmcqYuqGLNdE'
	),
	'shinelabs5' => array(
			'consumerKey' => 'WznhXVWXUdpmsTpY6XQHw',
			'consumerSecret' => 'qNodTsDpNe8SY1ceiDtTmW3SS9qYpbmkVFEeob0M',
			'accessToken' => '1928257328-iCMV7YxMmZMgmxryiJOOhNdYGkKMFIyZXB8kDxe',
			'accessTokenSecret' => 'gAKpwCoF5lXPogu0Pi7589mbBjMM8F3QmqBXIy0Aico'
	),
);

global $TWITTER_CONFIGS;

$MONGO_CONFIG = array(
	1 => array(
		'name' => 'Production',
		'url' => 'mongodb://10.144.72.17',
		'collection' => 'shine_production'),
	2 => array(
		'name' => 'Staging',
		'url' => 'mongodb://10.99.65.131',
		'collection' => 'shine_staging')	
);
global $MONGO_CONFIG;

define("TIMEZONE", "US/Pacific");
define("TIMEZONE_OFFSET", "-07:00");

date_default_timezone_set(TIMEZONE);

