<?php

namespace bitbetrieb\MVC\FrontController;

use bitbetrieb\MVC\HTTP\IResponse as IResponse;
use bitbetrieb\MVC\HTTP\IRequest as IRequest;

/**
 * Interface IRoute
 * @package bitbetrieb\MVC\FrontController
 */
interface IRoute {
    public function __construct($route, $httpMethod, $callable);
    public function setRoute($route);
    public function getRoute();
    public function setRouteRegex($route);
    public function getRouteRegex();
    public function setHttpMethod($httpMethod);
    public function getHttpMethod();
    public function initCallable($callable);
    public function setControllerClassName($controllerClassName);
    public function getControllerClassName();
    public function setControllerClassMethodName($controllerClassMethodName);
    public function getControllerClassMethodName();
    public function setUri($uri);
    public function getUri();
    public function addArgument($arg);
    public function setArguments(Array $args);
    public function getArguments();
    public function httpMethodsMatch($method);
    public function uriMatchesRegex($uri);
    public function combineInvocationArguments();
    public function getURIParameters();
    public function invoke();
}

?>