<?php

namespace bitbetrieb\CMS\FrontController;

use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;

class Route implements IRoute {
    private $routeRegex;
    private $httpMethod;
    private $controllerClassName;
    private $controllerClassMethodName;

    public function __construct($routeRegex, $httpMethod, $callable) {
        $this->setRegex($routeRegex);
        $this->setHttpMethod($httpMethod);
        $this->initCallable($callable);
    }

    public function setRegex($routeRegex) {
        $regex = preg_replace("/({.*?})/", "(.*)", $routeRegex);
        $regex = '/^' . str_replace('/', '\/', $regex) . '$/';

        $this->routeRegex = $regex;
    }

    public function getRegex() {
        return $this->routeRegex;
    }

    public function setHttpMethod($httpMethod) {
        $this->httpMethod = $httpMethod;
    }

    public function getHttpMethod() {
        return $this->httpMethod;
    }

    public function initCallable($callable) {
        $parts = explode("@", $callable);

        $this->controllerClassName = $parts[0];
        $this->controllerClassMethodName = $parts[1];
    }

    public function setControllerClassName($controllerClassName) {
        $this->controllerClassName = $controllerClassName;
    }

    public function getControllerClassName() {
        return Container::get('controller-namespace').$this->controllerClassName;
    }

    public function setControllerClassMethodName($controllerClassMethodName) {
        $this->controllerClassMethodName = $controllerClassMethodName;
    }

    public function getControllerClassMethodName() {
        return $this->controllerClassMethodName;
    }

    public function httpMethodsMatch($method) {
        return $method === $this->getHttpMethod();
    }

    public function uriMatchesRegex($uri) {
        $match = preg_match($this->getRegex(), $uri);

        return ($match === 1) ? true : false;
    }

    public function getInvocationArguments($request, $response) {
        $params = [];

        //Request und Response hinzufügen
        $params[] = $request;
        $params[] = $response;

        //URI Parameter auslesen und hinzufügen
        foreach($this->getURIParameters($request->uri()) as $param) {
            $params[] = $param;
        }

        return $params;
    }

    public function getURIParameters($uri) {
        $params = [];

        if(!empty($this->getRegex()) && preg_match($this->getRegex(), $uri, $params)) {
            array_shift($params);
        }

        return $params;
    }

    public function invoke($request, $response) {
        //Controller Klassen Reflektor erzeugen
        $controller = new \ReflectionClass($this->getControllerClassName());

        //Controller Methoden Reflektor erzeugen
        $method = new \ReflectionMethod($this->getControllerClassName(), $this->getControllerClassMethodName());

        //Controller mit Methode aufrufen und Parameter übergeben
        $method->invokeArgs($controller->newInstance(), $this->getInvocationArguments($request, $response));
    }
}

?>