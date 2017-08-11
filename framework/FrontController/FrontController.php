<?php

namespace bitbetrieb\CMS\FrontController;

use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;
use bitbetrieb\CMS\HTTP\IResponse as IResponse;
use bitbetrieb\CMS\HTTP\IRequest as IRequest;

class FrontController implements IFrontController {
    /**
     * Request Object
     *
     * @var IRequest
     */
    private $request;

    /**
     * Response Object
     *
     * @var IResponse
     */
    private $response;

    /**
     * Route Sammlung
     *
     * @var array
     */
    private $routes = [];

    /**
     * Error Handler
     *
     * @var string
     */
    private $errorHandler;

    /**
     * FrontController constructor.
     *
     * @param IRequest $request
     * @param $routesFile
     */
    public function __construct(IRequest $request, IResponse $response, $routesFile) {
        $this->request = $request;
        $this->response = $response;

        //Route Datei einbinden
        include($routesFile);
    }

    /**
     * GET Route hinzufügen
     *
     * @param string $route
     * @param string $callable
     */
    public function get($route, $callable) {
        $this->addRoute("GET", $route, $callable);
    }

    /**
     * POST Route hinzufügen
     *
     * @param string $route
     * @param string $callable
     */
    public function post($route, $callable) {
        $this->addRoute("POST", $route, $callable);
    }

    /**
     * PUT Route hinzufügen
     *
     * @param string $route
     * @param string $callable
     */
    public function put($route, $callable) {
        $this->addRoute("PUT", $route, $callable);
    }

    /**
     * DELETE Route hinzufügen
     *
     * @param string $route
     * @param string $callable
     */
    public function delete($route, $callable) {
        $this->addRoute("DELETE", $route, $callable);
    }

    /**
     * Route hinzufügen
     *
     * @param string $method
     * @param string $route
     * @param string $callable
     */
    public function addRoute($method, $route, $callable) {
        //Löse {platzhalter} der Route auf und ersetze sie durch den regulären Ausdruck (.*)
        $pattern = $this->replaceMarkers($route);

        //Escape Slashes der Route um regulären Ausdruck valide zu machen
        $pattern = $this->escapeSlashes($pattern);

        //Teile callable Zeichenkette auf in Controller und zugehörige Funktion
        $callable = $this->readCallable($callable);

        //Speichere Route
        $this->routes[] = [
            "pattern" => $pattern,
            "method" => $method,
            "controller" => $callable['controller'],
            "function" => $callable['function']
        ];
    }

    /**
     * Setzt Controller und Funktion für Fehlerfälle
     *
     * @param string $callable Zeichenkette in Form von "controller@funktion"
     */
    public function setErrorHandler($callable) {
        $this->errorHandler = $this->readCallable($callable);
    }

    /**
     * Request and Controller delegieren wenn Route existiert.
     * Wenn Route nicht existiert dann wird der Error Handler aufgerufen
     */
    public function execute() {
        $route = $this->resolveRoute();
        $arguments = $this->getRouteCallArguments($route);

        //Controller Namespace und Klasse des Controllers zusammenfügen
        $controllerClass = Container::get('controller-namespace') . $route['controller'];

        //Controller Klassen Reflektor erzeugen
        $controller = new \ReflectionClass($controllerClass);

        //Controller Methoden Reflektor erzeugen
        $method = new \ReflectionMethod($controllerClass, $route['function']);

        //Controller mit Methode aufrufen und Parameter übergeben
        $method->invokeArgs($controller->newInstance(), $arguments);
    }

    /**
     * Sucht die vom Nutzer aufgerufenen Route. Wird diese nicht gefunden wird der ErrorHandler zurückgegeben
     *
     * @return mixed|string
     */
    private function resolveRoute() {
        $result = $this->errorHandler;

        foreach($this->routes as $route) {
            if($this->routeMatchesMethod($route['method'])) {
                if($this->routeMatchesPattern($route['pattern'])) {
                    $result = $route;
                }
            }
        }

        return $result;
    }

    /**
     * Überprüft ob die vom Client benutzte Methode der Routen Methode entspricht
     *
     * @param $method
     * @return bool
     */
    private function routeMatchesMethod($method) {
        return $method === $this->request->method();
    }

    /**
     * Überprüft ob die vom Client aufgerufene URI mit dem regulären Ausdruck der Route übereinstimmt
     *
     * @param $pattern
     * @return bool
     */
    private function routeMatchesPattern($pattern) {
        $match = preg_match($pattern, $this->request->uri());

        return ($match === 1) ? true : false;
    }

    /**
     * Gibt die Parameter für den Funktionsaufruf des Subcontrollers zurück
     *
     * @param $route
     * @return array
     */
    private function getRouteCallArguments($route) {
        $params = [];

        $params[] = $this->request;
        $params[] = $this->response;

        $uriParams = $this->getURIParameters($route['pattern']);
        foreach($uriParams as $param) {
            $params[] = $param;
        }

        return $params;
    }

    /**
     * Gibt die in der URI enthaltenen Parameter zurück
     *
     * @param $pattern
     * @return array
     */
    private function getURIParameters($pattern) {
        $params = [];

        if(!empty($pattern) && preg_match($pattern, $this->request->uri(), $params)) {
            array_shift($params);
        }

        return $params;
    }

    /**
     * Verwandelt Route mit Platzhaltern zu einem regulären Ausdruck
     *
     * @param string $route Route mit Platzhaltern in Form von {platzhalter} z.B. /users/{name}/orders/{id}
     * @return string Regulärer Ausdruck mit ersetzten Platzhaltern z.B. /users/(.*)/orders/(.*)
     */
    private function replaceMarkers($route) {
        return preg_replace("/({.*?})/", "(.*)", $route);
    }

    /**
     * Escaped Slashes und fügt Delimiter an regulären Ausdruck an
     *
     * @param string $string Zeichenkette mit Slashes die escaped werden müssen
     * @return string Zeichenkette mit escapten Slashes
     */
    private function escapeSlashes($string) {
        return '/^' . str_replace('/', '\/', $string) . '$/';
    }

    /**
     * Callable Zeichenkette in Controller und Funktion aufteilen
     *
     * @param $string Callable Zeichenkette in Form von "controller@funktion"
     * @return array
     */
    private function readCallable($string) {
        $parts = explode("@", $string);

        return [
            'controller' => $parts[0],
            'function' => $parts[1]
        ];
    }
}

?>
