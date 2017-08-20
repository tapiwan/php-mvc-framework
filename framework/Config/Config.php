<?php

namespace bitbetrieb\CMS\Config;

/**
 * Class Config
 * @package bitbetrieb\CMS\Config
 */
class Config implements IConfig {
    /**
     * Config Map
     *
     * @var array|mixed
     */
    private $map = [];

    /**
     *
     */
    public function __construct($file) {
        $this->load($file);
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

    /**
     * Konfigurationsdatei laden
     *
     * @param $file
     */
    private function load($file) {
        $this->map = json_decode(file_get_contents($file), true);
    }
}

?>