<?php

namespace bitbetrieb\MVC\Config;

/**
 * Interface IConfig
 * @package bitbetrieb\MVC\Config
 */
interface IConfig {
    public static function get($key);
    public static function set($key, $value);
}

?>
