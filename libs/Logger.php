<?php
class Logger {
	public static function log($message) {
		echo date("Y-m-d h:i:s") . " | " . $message . "\n";
	}
}
