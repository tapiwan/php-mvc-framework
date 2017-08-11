<?php

namespace bitbetrieb\CMS\FrontController;

use bitbetrieb\CMS\HTTP\IResponse as IResponse;
use bitbetrieb\CMS\HTTP\IRequest as IRequest;

interface IFrontController {
    public function __construct(IRequest $request, IResponse $response, $routesFile);
    public function get($route, $callable);
    public function post($route, $callable);
    public function put($route, $callable);
    public function delete($route, $callable);
    public function addRoute($method, $route, $callable);
    public function setErrorHandler($callable);
    public function execute();
}

?>
