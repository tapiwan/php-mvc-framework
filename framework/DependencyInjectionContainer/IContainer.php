<?php

namespace bitbetrieb\MVC\DependencyInjectionContainer;

/**
 * Interface IContainer
 * @package bitbetrieb\MVC\DependencyInjectionContainer
 */
interface IContainer {
    public static function addValue($id, $value);
    public static function addClass($id, $class, $dependencies);
    public static function addSingleton($id, $class, $dependencies);
    public static function initializeViaJSON($json);
    public static function has($id);
    public static function get($id);
}

?>
