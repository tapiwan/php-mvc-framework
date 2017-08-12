<?php

namespace bitbetrieb\CMS\FrontController;

use bitbetrieb\CMS\HTTP\IResponse as IResponse;
use bitbetrieb\CMS\HTTP\IRequest as IRequest;

/**
 * Interface IRoute
 * @package bitbetrieb\CMS\FrontController
 */
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
    public function getInvocationArguments(IRequest $request, IResponse $response);
    public function getURIParameters($uri);
    public function invoke(IRequest $request, IResponse $response);
}

?>