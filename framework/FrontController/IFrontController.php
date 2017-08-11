<?php

namespace bitbetrieb\CMS\FrontController;

interface IFrontController {
    public function get($route, $callable);
    public function post($route, $callable);
    public function put($route, $callable);
    public function delete($route, $callable);
    public function addRoute($method, $route, $callable);
    public function setErrorHandler($callable);
    public function execute();
}

?>
