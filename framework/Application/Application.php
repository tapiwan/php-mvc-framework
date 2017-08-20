<?php

namespace bitbetrieb\CMS\Application;

use bitbetrieb\CMS\Config\IConfig as IConfig;
use bitbetrieb\CMS\FrontController\IFrontController as IFrontController;
use bitbetrieb\CMS\Template\Template as Template;

/**
 * Class Application
 * @package bitbetrieb\CMS\Application
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
        Template::setViewDirectory(APP_PATH.$this->config->get('view-directory'));

        //Front Controller einstellen
        $this->frontController->setControllerNamespacePrefix($this->config->get('controller-namespace'));
        $this->frontController->addExtension(APP_PATH.$this->config->get('routes-file'));

        //Front Controller ausführen
        $this->frontController->execute();
    }
}

?>
