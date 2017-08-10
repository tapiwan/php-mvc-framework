<?php

namespace bitbetrieb\CMS\Config;

interface IConfig {
    public function __construct($pathToConfigFile);
    public function get($key);
    public function set($key, $value);
}

?>
