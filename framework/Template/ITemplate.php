<?php

namespace bitbetrieb\CMS\Template;

/**
 * Interface ITemplate
 * @package bitbetrieb\CMS\Template
 */
interface ITemplate {
    public function __construct($file, $vars = []);
    public function __set($key, $value);
    public function __get($key);
    public function set($key, $value);
    public function get($key);
    public function render();
    public function display();
    public function extend($file);
    public function inc($file);
}

?>