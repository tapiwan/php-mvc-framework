<?php

namespace bitbetrieb\CMS\Application;

use bitbetrieb\CMS\FrontController\IFrontController as IFrontController;

/**
 * Class Application
 * @package bitbetrieb\CMS\Application
 */
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
     * Starte die App
     */
    public function start() {
        $this->frontController->execute();
    }
}

?>
