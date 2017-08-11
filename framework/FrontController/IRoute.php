<?php

namespace bitbetrieb\CMS\FrontController;

interface IRoute {
    public function __construct($routeRegex, $httpMethod, $callable);
    public function setRegex($routeRegex);
    public function getRegex();
    public function setHttpMethod($httpMethod);
    public function getHttpMethod();
    public function initCallable($callable);
    public function setControllerClassName($controllerClassName);
    public function getControllerClassName();
    public function setControllerClassMethodName($controllerClassMethodName);
    public function getControllerClassMethodName();
    public function httpMethodsMatch($method);
    public function uriMatchesRegex($uri);
    public function getInvocationArguments($request, $response);
    public function getURIParameters($uri);
    public function invoke($request, $response);
}

?>