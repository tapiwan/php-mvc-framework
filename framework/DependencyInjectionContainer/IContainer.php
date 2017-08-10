<?php

namespace bitbetrieb\CMS\DependencyInjectionContainer;

interface IContainer {
    public static function addValue($id, $value);
    public static function addClass($id, $class, $dependencies);
    public static function addSingleton($id, $class, $dependencies);
    public static function get($id);
}

?>
