<?php

namespace bitbetrieb\MVC\Template;

/**
 * Interface ITemplate
 * @package bitbetrieb\MVC\Template
 */
interface ITemplate {
    public function __construct($file, $vars = [], $fileDirectory = "");
    public function __set($key, $value);
    public function __get($key);
    public static function setViewDirectory($directory);
    public function setFileDirectory($directory);
    public function render();
    public function display();
    public function extend($file);
    public function load($file);
}

?>