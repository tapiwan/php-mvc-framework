<?php

namespace bitbetrieb\MVC\Config;

/**
 * Class Config
 * @package bitbetrieb\MVC\Config
 */
class Config implements IConfig {
	/**
	 * Config Map
	 *
	 * @var array|mixed
	 */
	private static $map = [];

	/**
	 * Wert der Config zurückgeben
	 *
	 * @param $key
	 * @return bool|mixed
	 */
	public static function get($key) {
		$accessors = explode('/', $key);

		$current = &self::$map;

		foreach ($accessors as $accessor) {
			if (!isset($current[$accessor])) return false;

			$current = &$current[$accessor];
		}

		return $current;
	}

	/**
	 * Wert der Config setzen
	 *
	 * @param $key
	 * @param $value
	 */
	public static function set($key, $value) {
		$accessors = explode('/', $key);

		$i       = 0;
		$len     = count($accessors) - 1;
		$current = &self::$map;

		foreach ($accessors as $accessor) {
			if (!isset($current[$accessor])) $current[$accessor] = [];

			$current = &$current[$accessor];
		}

		$current = $value;
	}

	/**
	 * Konfigurationsdatei laden
	 *
	 * @param $file
	 */
	public static function loadJSON($file) {
		if (file_exists($file)) {
			self::$map = json_decode(file_get_contents($file), true);
		}
	}
}

?>