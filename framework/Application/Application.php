<?php

namespace bitbetrieb\CMS\Application;

use bitbetrieb\CMS\FrontController\IFrontController as IFrontController;

class Application {
    /**
     * Front Controller der App
     *
     * @var IFrontController
     */
    private $frontController;

    /**
     * Application constructor.
     *
     * @param IFrontController $frontController
     */
    public function __construct(IFrontController $frontController) {
        $this->frontController = $frontController;
    }

    /**
     * Starte den Front Controller
     */
    public function start() {
        $this->frontController->execute();
    }
}

?>
