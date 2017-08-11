<?php

namespace bitbetrieb\CMS\FrontController;

interface IRoute {
    public function __construct($routeRegex, $httpMethod, $callable);
    public function getRegex();
    public function getHttpMethod();
    public function getControllerClassName();
    public function getControllerClassMethodName();
}

?>