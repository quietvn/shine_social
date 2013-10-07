<?php
class Logger {
	public static function log($message) {
		echo date("Y-m-d H:i:s") . " | " . $message . "\n";
	}
}
