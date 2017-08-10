<?php

namespace bitbetrieb\CMS\Application;

class Application {
    private $frontController;

    public function __construct($frontController) {
        $this->frontController = $frontController;
    }

    public function start() {
        $this->frontController->handleRequest();
    }
}

?>
