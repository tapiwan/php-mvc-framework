<?php

namespace bitbetrieb\MVC\Application;

use bitbetrieb\MVC\Config\IConfig as IConfig;
use bitbetrieb\MVC\FrontController\IFrontController as IFrontController;
use bitbetrieb\MVC\Template\Template as Template;

/**
 * Class Application
 * @package bitbetrieb\MVC\Application
 */
class Application {
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
    public function __construct(IConfig $config, IFrontController $frontController) {
        $this->config = $config;
        $this->frontController = $frontController;
    }

    /**
     * Starte die App
     */
    public function start() {
        //Pfad für Templates einstellen
        Template::setViewDirectory(APP_PATH.$this->config->get('directories/views'));

        //Front Controller einstellen
        $this->frontController->setControllerNamespacePrefix($this->config->get('controller/namespace'));
        $this->frontController->addExtension(APP_PATH.$this->config->get('files/routes'));

        //Front Controller ausführen
        $this->frontController->execute();
    }
}

?>
