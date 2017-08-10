<?php

namespace bitbetrieb\CMS\Config;

class Config implements IConfig {
    private $map = [];

    public function __construct($pathToConfigFile) {
        $this->map = json_decode(file_get_contents(realpath($pathToConfigFile)), true);
    }

    public function get($key) {
        if(!isset($this->map[$key])) return false;

        return $this->map[$key];
    }

    public function set($key, $value) {
        $this->map[$key] = $value;
    }
}

?>