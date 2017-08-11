<?php

namespace bitbetrieb\CMS\FrontController;

use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;
use bitbetrieb\CMS\HTTP\IResponse as IResponse;
use bitbetrieb\CMS\HTTP\IRequest as IRequest;
use bitbetrieb\CMS\FrontController\Route as Route;

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
     * @var array
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
        $this->loadRoutes($routesFile);
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
     * @param string $httpMethod
     * @param string $route
     * @param string $callable
     */
    public function addRoute($httpMethod, $route, $callable) {
        $this->routes[] = new Route($route, $httpMethod, $callable);
    }

    /**
     * Setzt Controller und Funktion für Fehlerfälle
     *
     * @param string $callable Zeichenkette in Form von "controller@funktion"
     */
    public function setErrorHandler($callable) {
        $this->errorHandler = new Route(null, null, $callable);
    }

    /**
     * Request and Controller delegieren wenn Route existiert.
     * Wenn Route nicht existiert dann wird der Error Handler aufgerufen
     */
    public function execute() {
        $route = $this->findRoute();

        $route->invoke($this->request, $this->response);
    }

    /**
     * Lädt die Web Routen
     *
     * @param $file
     */
    private function loadRoutes($file) {
        require($file);
    }

    /**
     * Sucht die vom Nutzer aufgerufenen Route. Wird diese nicht gefunden wird der ErrorHandler zurückgegeben
     *
     * @return mixed|string
     */
    private function findRoute() {
        $result = $this->errorHandler;

        foreach($this->routes as $route) {
            if($route->httpMethodsMatch($this->request->method())) {
                if($route->uriMatchesRegex($this->request->uri())) {
                    $result = $route;
                }
            }
        }

        return $result;
    }
}

?>
