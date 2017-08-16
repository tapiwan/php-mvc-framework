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
	public function __isset($key);
	public function __unset($key);
	public function render();
	public function display();
	public function extend($file);
	public function load($file);
}

?>