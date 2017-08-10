<?php

namespace bitbetrieb\CMS\Config;

class Config implements IConfig {
    /**
     * Config Map
     *
     * @var array|mixed
     */
    private $map = [];

    /**
     * Config constructor.
     *
     * @param string $pathToConfigFile Pfad zur Config Datei
     */
    public function __construct($pathToConfigFile) {
        $this->map = json_decode(file_get_contents(realpath($pathToConfigFile)), true);
    }

    /**
     * Wert der Config zurückgeben
     *
     * @param $key
     * @return bool|mixed
     */
    public function get($key) {
        if(!isset($this->map[$key])) return false;

        return $this->map[$key];
    }

    /**
     * Wert der Config setzen
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value) {
        $this->map[$key] = $value;
    }
}

?>