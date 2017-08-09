<?php

namespace bitbetrieb\CMS\ServiceContainer;

interface IContainer {
   public function addService($id, $class, $dependencies);
   public function get($id);
}

?>
