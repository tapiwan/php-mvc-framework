<?php

namespace bitbetrieb\CMS\Application;

use bitbetrieb\CMS\FrontController\IFrontController as IFrontController;
use bitbetrieb\CMS\Config\IConfig as IConfig;

class Application {
    private $frontController;
    private $config;

    public function __construct(IFrontController $frontController, IConfig $config) {
        global $container;

        $this->frontController = $frontController;
        $this->config = $config;

        print_r($container);
    }

    public function document_root() {
        return $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR;
    }

    public function base_dir() {
        return dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR;
    }

    public function dir($path = "") {
        $path = str_replace("/", DIRECTORY_SEPARATOR, $path);
        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);

        return $this->base_dir().$path;
    }
}

?>
