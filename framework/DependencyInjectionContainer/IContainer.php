<?php

namespace bitbetrieb\CMS\DependencyInjectionContainer;

/**
 * Interface IContainer
 * @package bitbetrieb\CMS\DependencyInjectionContainer
 */
interface IContainer {
    public function addValue($id, $value);
    public function addClass($id, $class, $dependencies);
    public function addSingleton($id, $class, $dependencies);
    public function initializeViaJSON($json);
    public function has($id);
    public function get($id);
}

?>
