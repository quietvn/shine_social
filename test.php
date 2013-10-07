<?php
//echo time();

define("TIMEZONE", "US/Pacific");
define("TIMEZONE_OFFSET", "-07:00");

date_default_timezone_set(TIMEZONE);

echo date('Y-m-d H:i:s', 1381042800);
echo date('Y-m-d H:i:s', time());