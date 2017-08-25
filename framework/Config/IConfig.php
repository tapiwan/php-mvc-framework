<?php

namespace bitbetrieb\MVC\Config;

/**
 * Interface IConfig
 * @package bitbetrieb\MVC\Config
 */
interface IConfig {
    public function __construct($file);
    public function get($key);
    public function set($key, $value);
}

?>
