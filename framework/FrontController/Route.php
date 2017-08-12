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
     * Regulärer Ausdruck
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
     * Route constructor.
     *
     * @param string $routeRegex Route mit Platzhaltern in Form von "/diese/route/hat/{variable1}/und/{variable2}"
     * @param string $httpMethod HTTP Methode
     * @param string $callable Zeichenkette in Form von "controller@funktion"
     */
    public function __construct($routeRegex, $httpMethod, $callable) {
        $this->setRegex($routeRegex);
        $this->setHttpMethod($httpMethod);
        $this->initCallable($callable);
    }

    /**
     * Baut die Route zu regulärem Ausdruck um und speichert ihn
     *
     * @param string $routeRegex Route mit Platzhaltern in Form von "/diese/route/hat/{variable1}/und/{variable2}"
     */
    public function setRegex($routeRegex) {
        //Platzhalter {} durch regulären Ausdruck (.*) ersetzen
        $regex = preg_replace("/({.*?})/", "(.*)", $routeRegex);

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
    public function getRegex() {
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
        return Container::get('controller-namespace').$this->controllerClassName;
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
        $match = preg_match($this->getRegex(), $uri);

        return ($match === 1) ? true : false;
    }

    /**
     * Sammle die nötigen Parameter die an die Controllermethode übergeben werden bei Aktivierung der Route
     *
     * @param IRequest $request
     * @param IResponse $response
     *
     * @return array Enthält Parameter die an die Controllermethode übergeben werden. Enthält mindestens Request und
     * Response als jeweils erstes und zweites Element. Weitere Elemente sind die aus der URI ausgelesen Parameter.
     */
    public function getInvocationArguments(IRequest $request, IResponse $response) {
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

    /**
     * Liest Parameter aus einer URI aus
     *
     * @param string $uri Auszulesende URI
     *
     * @return array Enthält aus der URI ausgelesene Parameter als Schlüssel-Wert Paar. Wenn es keine Parameter
     * gibt ist es ein leeres Array
     */
    public function getURIParameters($uri) {
        $params = [];

        if(!empty($this->getRegex()) && preg_match($this->getRegex(), $uri, $params)) {
            array_shift($params);
        }

        return $params;
    }

    /**
     * Aktiviere die Route. Der Controller wird erzeugt und die Methode wird mit den notwendigen Parametern aufgerufen
     *
     * @param IRequest $request
     * @param IResponse $response
     */
    public function invoke(IRequest $request, IResponse $response) {
        //Controller Klassen Reflektor erzeugen
        $controller = new \ReflectionClass($this->getControllerClassName());

        //Controller Methoden Reflektor erzeugen
        $method = new \ReflectionMethod($this->getControllerClassName(), $this->getControllerClassMethodName());

        //Controller mit Methode aufrufen und Parameter übergeben
        $method->invokeArgs($controller->newInstance(), $this->getInvocationArguments($request, $response));
    }
}

?>