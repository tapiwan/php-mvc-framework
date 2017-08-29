<?php

namespace bitbetrieb\MVC\FrontController;

use bitbetrieb\MVC\HTTP\IResponse as IResponse;
use bitbetrieb\MVC\HTTP\IRequest as IRequest;
use bitbetrieb\MVC\FrontController\Route as Route;

/**
 * Class FrontController
 * @package bitbetrieb\MVC\FrontController
 */
class FrontController implements IFrontController {
    /**
     * Request Object
     *
     * @var IRequest
     */
    private $request;

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
     * Extensions Array
     *
     * @var array
     */
    private $extensions = [];

    /**
     * Flag ob Extensions geladen wurden
     */
    private $extensionsLoaded = false;

    /**
     * Parameter welche an alle Routen übergeben werden sollen
     */
    private $routeParameters = [];

    /**
     * Namespace Prefix der Controller, optional
     *
     * @var string
     */
    private $controllerNamespacePrefix = "";

    /**
     * FrontController constructor.
     *
     * @param IRequest $request
     */
    public function __construct(IRequest $request) {
        $this->request = $request;
    }

    /**
     * Fügt dem Front Controller eine Extension hinzu
     *
     * @param $file
     */
    public function addExtension($file) {
        $this->extensions[] = $file;
    }

    /**
     * Lädt eine Extension
     *
     * @param string $file Pfad zur Extension Datei
     */
    public function loadExtension($file) {
        require_once $file;
    }

    /**
     * Lädt alle Extensions des Front Controllers
     */
    public function loadExtensions() {
        if(count($this->extensions) > 0 && !$this->extensionsLoaded) {
            foreach($this->extensions as $extension) {
                if(file_exists($extension)) {
                    $this->loadExtension($extension);
                }
            }
        }

        $this->extensionsLoaded = true;
    }

    /**
     * Fügt einen Routen Parameter hinzu
     *
     * @param mixed $parameter
     */
    public function addRouteParameter($parameter) {
        $this->routeParameters[] = $parameter;
    }

    /**
     * Gibt die Routen Parameter zurück
     *
     * @return array
     */
    public function getRouteParameters() {
        return $this->routeParameters;
    }

    /**
     * Setzt den Namespace der Controller
     *
     * @param string $controllerNamespacePrefix
     */
    public function setControllerNamespacePrefix($controllerNamespacePrefix) {
        $this->controllerNamespacePrefix = $controllerNamespacePrefix;
    }

    /**
     * Gibt den Namespace Prefix der Controller zurück
     *
     * @return string
     */
    public function getControllerNamespacePrefix() {
        return $this->controllerNamespacePrefix;
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
        $this->routes[] = new Route($route, $httpMethod, $this->getPrefixedCallable($callable));
    }

    /**
     * Setzt Callable für Fehlerfälle
     *
     * @param string $callable Zeichenkette in Form von "controller@funktion"
     */
    public function setErrorHandler($callable) {
        $this->errorHandler = new Route(null, null, $this->getPrefixedCallable($callable));
    }

    /**
     * Adds the controller namespace prefix to the callable
     *
     * @param $callable
     * @return string
     */
    private function getPrefixedCallable($callable) {
        return $this->getControllerNamespacePrefix().$callable;
    }

    /**
     * Passende Route ausfindig machen.
     * Wenn Route existiert diese ausführen und Request und Response übergeben
     * Wenn Route nicht existiert dann wird der Error Handler aufgerufen, ebenfalls mit Request und Response
     */
    public function execute() {
        //Load extensions if not loaded yet
        $this->loadExtensions();

        //Find route
        $route = $this->findRoute();

        //Add request to route parameters
        $this->addRouteParameter($this->request);

        //Set the invocation arguments of the route
        $route->setArguments($this->getRouteParameters());

        //Set the uri of the route
        $route->setUri($this->request->uri());

        //Invoke
        $route->invoke();
    }

    /**
     * Sucht die vom Nutzer aufgerufenen Route. Wird diese nicht gefunden wird der Error Handler zurückgegeben
     *
     * @return IRoute $result
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
