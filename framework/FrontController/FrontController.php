<?php

namespace bitbetrieb\CMS\FrontController;

use bitbetrieb\CMS\HTTP\IResponse as IResponse;
use bitbetrieb\CMS\HTTP\IRequest as IRequest;
use bitbetrieb\CMS\FrontController\Route as Route;

/**
 * Class FrontController
 * @package bitbetrieb\CMS\FrontController
 */
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
     * Routen Array
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
     * @param IResponse $response
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
     * @param string $route Route mit Platzhaltern in Form von "/diese/route/hat/{variable1}/und/{variable2}"
     * @param string $callable Zeichenkette in Form von "controller@funktion"
     */
    public function get($route, $callable) {
        $this->addRoute("GET", $route, $callable);
    }

    /**
     * POST Route hinzufügen
     *
     * @param string $route Route mit Platzhaltern in Form von "/diese/route/hat/{variable1}/und/{variable2}"
     * @param string $callable Zeichenkette in Form von "controller@funktion"
     */
    public function post($route, $callable) {
        $this->addRoute("POST", $route, $callable);
    }

    /**
     * PUT Route hinzufügen
     *
     * @param string $route Route mit Platzhaltern in Form von "/diese/route/hat/{variable1}/und/{variable2}"
     * @param string $callable Zeichenkette in Form von "controller@funktion"
     */
    public function put($route, $callable) {
        $this->addRoute("PUT", $route, $callable);
    }

    /**
     * DELETE Route hinzufügen
     *
     * @param string $route Route mit Platzhaltern in Form von "/diese/route/hat/{variable1}/und/{variable2}"
     * @param string $callable Zeichenkette in Form von "controller@funktion"
     */
    public function delete($route, $callable) {
        $this->addRoute("DELETE", $route, $callable);
    }

    /**
     * Route hinzufügen
     *
     * @param string $httpMethod HTTP Methode
     * @param string $route Route mit Platzhaltern in Form von "/diese/route/hat/{variable1}/und/{variable2}"
     * @param string $callable Zeichenkette in Form von "controller@funktion"
     */
    public function addRoute($httpMethod, $route, $callable) {
        $this->routes[] = new Route($route, $httpMethod, $callable);
    }

    /**
     * Setzt Callable für Fehlerfälle
     *
     * @param string $callable Zeichenkette in Form von "controller@funktion"
     */
    public function setErrorHandler($callable) {
        $this->errorHandler = new Route(null, null, $callable);
    }

    /**
     * Passende Route ausfindig machen.
     * Wenn Route existiert diese ausführen und Request und Response übergeben
     * Wenn Route nicht existiert dann wird der Error Handler aufgerufen, ebenfalls mit Request und Response
     */
    public function execute() {
        $route = $this->findRoute();

        $route->invoke($this->request, $this->response);
    }

    /**
     * Lädt die Routen
     *
     * @param string $file Pfad zur Routen Datei
     */
    private function loadRoutes($file) {
        require($file);
    }

    /**
     * Sucht die vom Nutzer aufgerufenen Route. Wird diese nicht gefunden wird der Error Handler zurückgegeben
     *
     * @return object
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
