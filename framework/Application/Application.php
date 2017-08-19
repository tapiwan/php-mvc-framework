<?php

namespace bitbetrieb\CMS\Application;

use bitbetrieb\CMS\DependencyInjectionContainer\IContainer as IContainer;
use bitbetrieb\CMS\Config\IConfig as IConfig;
use bitbetrieb\CMS\FrontController\IFrontController as IFrontController;

/**
 * Class Application
 * @package bitbetrieb\CMS\Application
 */
class Application {
    /**
     * IoC Container der App
     */
    private $container;

    /**
     * Konfiguration der App
     */
    private $config;

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
    public function __construct(IContainer $container, IConfig $config, IFrontController $frontController) {
        $this->container = $container;
        $this->config = $config;
        $this->frontController = $frontController;
    }

    /**
     * Starte die App
     */
    public function start() {
        \bitbetrieb\CMS\Template\Template::setViewDirectory(APP_PATH."/resources/views");

        $this->frontController->setControllerNamespacePrefix('\\bitbetrieb\\CMS\\Controller\\');
        $this->frontController->addExtension(APP_PATH."/app/routes.php");

        $this->frontController->execute();
    }
}

?>
