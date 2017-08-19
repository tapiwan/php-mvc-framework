<?php

namespace bitbetrieb\CMS\FrontController;

use bitbetrieb\CMS\HTTP\IResponse as IResponse;
use bitbetrieb\CMS\HTTP\IRequest as IRequest;

/**
 * Interface IFrontController
 * @package bitbetrieb\CMS\FrontController
 */
interface IFrontController {
    public function __construct(IRequest $request);
    public function addExtension($file);
    public function loadExtension($file);
    public function loadExtensions();
    public function addRouteParameter($parameter);
    public function getRouteParameters();
    public function setControllerNamespacePrefix($controllerNamespacePrefix);
    public function getControllerNamespacePrefix();
    public function get($route, $callable);
    public function post($route, $callable);
    public function put($route, $callable);
    public function delete($route, $callable);
    public function addRoute($method, $route, $callable);
    public function setErrorHandler($callable);
    public function execute();
}

?>
