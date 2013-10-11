<?php
class Cache {
	const CACHE_PATH = 'cache';
	public static function get($key) {
		$file = self::CACHE_PATH . "/$key.cache";
		if (file_exists($file)) {
			return json_decode(file_get_contents($file));
		}
		return null;
	}
	
	public static function save($key, $value) {
		$file = self::CACHE_PATH . "/$key.cache";
		return file_put_contents($file, json_encode($value));
	}
}
