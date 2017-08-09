<?php

namespace bitbetrieb\CMS\DependencyInjectionContainer;

interface IContainer {
    public function addValue($id, $value);

    public function addClass($id, $class, $dependencies);

    public function addSingleton($id, $class, $dependencies);

    public function get($id);
}

?>
