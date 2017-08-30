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
	 * @var array<Route>
	 */
	private $routes = [];

	/**
	 * Error Handler
	 *
	 * @var Route
	 */
	private $errorHandler;

	/**
	 * FrontController constructor.
	 *
	 * @param IRequest $request
	 */
	public function __construct(IRequest $request) {
		$this->request = $request;
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
		return "\\bitbetrieb\\MVC\\Controller\\".$callable;
	}

	/**
	 * Passende Route ausfindig machen.
	 * Wenn Route existiert diese ausführen und Request und Response übergeben
	 * Wenn Route nicht existiert dann wird der Error Handler aufgerufen, ebenfalls mit Request und Response
	 */
	public function execute() {
		//Find route
		$route = $this->findRoute();

		//Add the request to the arguments of the route
		$route->addArgument($this->request);

		//Invoke the route via the URI
		$route->invoke($this->request->uri());
	}

	/**
	 * Lädt eine Konfigurationsdatei des Front Controllers
	 *
	 * @param string $file Pfad zur Konfigurationsdatei
	 */
	public function load($file) {
		require_once($file);
	}

	/**
	 * Sucht die vom Nutzer aufgerufenen Route. Wird diese nicht gefunden wird der Error Handler zurückgegeben
	 *
	 * @return IRoute $result
	 */
	private function findRoute() {
		$result = $this->errorHandler;

		foreach ($this->routes as $route) {
			if ($route->matches($this->request)) {
				$result = $route;
			}
		}

		return $result;
	}
}

?>
