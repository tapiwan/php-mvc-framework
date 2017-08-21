<?php

namespace bitbetrieb\CMS\Application;

use bitbetrieb\CMS\Config\IConfig;
use bitbetrieb\CMS\FrontController\IFrontController;
use bitbetrieb\CMS\Template\Template;

/**
 * Class Application
 * @package bitbetrieb\CMS\Application
 */
class Application {
    /**
     * Konfiguration der App
     *
     * @var IConfig
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
     * @param IConfig $config
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

        //Front Controller den Namespace der Controller mitteilen
        $this->frontController->setControllerNamespacePrefix($this->config->get('controller/namespace'));

        //Front Controller Routen laden
        $this->frontController->addExtension(APP_PATH.$this->config->get('files/routes'));

        //Front Controller ausführen
        $this->frontController->execute();
    }
}

?>
