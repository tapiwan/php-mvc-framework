<?php

namespace bitbetrieb\CMS\FrontController;

use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;
use bitbetrieb\CMS\HTTP\IResponse as IResponse;
use bitbetrieb\CMS\HTTP\IRequest as IRequest;

/**
 * Class Route
 * @package bitbetrieb\CMS\FrontController
 */
class Route implements IRoute {
    /**
     * Die original Route
     *
     * @var string
     */
    private $route;

    /**
     * Regulärer Ausdruck der Route
     *
     * @var string
     */
    private $routeRegex;

    /**
     * HTTP Methode
     *
     * @var string
     */
    private $httpMethod;

    /**
     * Klassenname des aufzurufenden Controllers mit Namespace
     *
     * @var string
     */
    private $controllerClassName;

    /**
     * Methodenname der am Controller aufzurufenden Methode
     *
     * @var string
     */
    private $controllerClassMethodName;

    /**
     * URI des Requests
     *
     * @var string
     */
    private $uri;

    /**
     * Argumente mit denen die Controllermethode aufgerufen werden soll
     *
     * @var array
     */
    private $arguments = [];

    /**
     * Route constructor.
     *
     * @param string $route Route mit Platzhaltern in Form von "/diese/route/hat/{variable1}/und/{variable2}"
     * @param string $httpMethod HTTP Methode
     * @param string $callable Zeichenkette in Form von "controller@funktion"
     */
    public function __construct($route, $httpMethod, $callable) {
        $this->setRoute($route);
        $this->setRouteRegex($route);
        $this->setHttpMethod($httpMethod);
        $this->initCallable($callable);
    }

    /**
     * Setzt die original Route
     *
     * @param $route
     */
    public function setRoute($route) {
        $this->route = $route;
    }

    /**
     * Gibt die original Route zurück
     */
    public function getRoute() {
        return $this->route;
    }

    /**
     * Baut die Route zu regulärem Ausdruck um und speichert ihn
     *
     * @param string $route Route mit Platzhaltern in Form von "/diese/route/hat/{variable1}/und/{variable2}"
     */
    public function setRouteRegex($route) {
        //Platzhalter {} durch regulären Ausdruck (.*) ersetzen
        $regex = preg_replace("/({.*?})/", "(.*)", $route);

        //Slashes in regulärem Ausdruck ersetzen
        $regex = str_replace('/', '\/', $regex);

        //Delimiter an regulären Ausdruck anhängen
        $regex = '/^' . $regex  . '$/';

        $this->routeRegex = $regex;
    }

    /**
     * Gibt regulären Ausdruck zurück
     *
     * @return string Regulärer Ausdruck der Route
     */
    public function getRouteRegex() {
        return $this->routeRegex;
    }

    /**
     * Setze HTTP Methode
     *
     * @param string $httpMethod
     */
    public function setHttpMethod($httpMethod) {
        $this->httpMethod = $httpMethod;
    }

    /**
     * Gibt die HTTP Methode zurück
     *
     * @return string HTTP Methode
     */
    public function getHttpMethod() {
        return $this->httpMethod;
    }

    /**
     * Löst die Zeichenkette in Controllername und Funktionsname auf und speichert sie
     *
     * @param string $callable Zeichenkette in Form von "controller@funktion"
     */
    public function initCallable($callable) {
        $parts = explode("@", $callable);

        $this->setControllerClassName($parts[0]);
        $this->setControllerClassMethodName($parts[1]);
    }

    /**
     * Setze Klassenname des Controllers
     *
     * @param string $controllerClassName
     */
    public function setControllerClassName($controllerClassName) {
        $this->controllerClassName = $controllerClassName;
    }

    /**
     * Gibt den Klassenname des Controllers zurück
     *
     * @return string Klassenname des Controllers
     */
    public function getControllerClassName() {
        return $this->controllerClassName;
    }

    /**
     * Setze Methodenname der am Controller aufzurufenden Methode
     *
     * @param string $controllerClassMethodName Methodenname der am Controller aufzurufenden Methode
     */
    public function setControllerClassMethodName($controllerClassMethodName) {
        $this->controllerClassMethodName = $controllerClassMethodName;
    }

    /**
     * Gibt Methodenname der am Controller aufzurufenden Methode zurück
     *
     * @return string Methodenname der am Controller aufzurufenden Methode
     */
    public function getControllerClassMethodName() {
        return $this->controllerClassMethodName;
    }

    /**
     * Setzt die URI, aus der die Parameter ausgelesen werden sollen
     *
     * @param $uri
     */
    public function setUri($uri) {
        $this->uri = $uri;
    }

    /**
     * Gibt die URI des Requests zurück
     *
     * @param $uri
     * @return string
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Fügt ein Argument für die Controllermethode hinzu
     *
     * @param mixed $arg
     */
    public function addArgument($arg) {
        $this->arguments[] = $arg;
    }

    /**
     * Setzt die Argumente für die Controllermethode
     *
     * @param array $args
     */
    public function setArguments(Array $args) {
        $this->arguments = $args;
    }

    /**
     * Gibt die Argumente der Controllermethode zurück
     *
     * @return array
     */
    public function getArguments() {
        return $this->arguments;
    }

    /**
     * Vergleicht HTTP Methode der Route mit übergebener Methode
     *
     * @param string $method Zu vergleichende HTTP Methode
     *
     * @return bool Enthält true wenn Methoden übereinstimmen, false wenn nicht
     */
    public function httpMethodsMatch($method) {
        return $method === $this->getHttpMethod();
    }

    /**
     * Vergleicht URI der Route mit übergebener URI
     *
     * @param string $uri Zu vergleichende URI
     *
     * @return bool Enthält true wenn URI übereinstimmen, false wenn nicht
     */
    public function uriMatchesRegex($uri) {
        $match = preg_match($this->getRouteRegex(), $uri);

        return ($match === 1) ? true : false;
    }

    /**
     * Fügt an die Routen Parameter noch die URI Parameter hinzu
     *
     * @param array $routeParameters
     */
    public function combineInvocationArguments() {
        //URI Parameter auslesen und hinzufügen
        foreach($this->getURIParameters() as $param) {
            $this->arguments[] = $param;
        }
    }

    /**
     * Liest Parameter aus einer URI aus
     *
     * @param string $uri Auszulesende URI
     *
     * @return array Enthält aus der URI ausgelesene Parameter als Schlüssel-Wert Paar. Wenn es keine Parameter
     * gibt ist es ein leeres Array
     */
    public function getURIParameters() {
        $params = [];

        if(!empty($this->getRouteRegex()) && preg_match($this->getRouteRegex(), $this->getUri(), $params)) {
            array_shift($params);
        }

        return $params;
    }

    /**
     * Aktiviere die Route.
     * Der Controller wird erzeugt und die Methode wird mit den notwendigen Parametern aufgerufen
     */
    public function invoke() {
        //URI Parameter an gegebene Parameter anhängen
        $this->combineInvocationArguments();

        //Controller Klassen Reflektor erzeugen
        $controller = new \ReflectionClass($this->getControllerClassName());

        //Controller Methoden Reflektor erzeugen
        $method = new \ReflectionMethod($this->getControllerClassName(), $this->getControllerClassMethodName());

        //Controller mit Methode aufrufen und Parameter übergeben
        $method->invokeArgs($controller->newInstance(), $this->getArguments());
    }
}

?>