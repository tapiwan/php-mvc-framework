<?php

namespace bitbetrieb\MVC\FrontController;

use bitbetrieb\MVC\DependencyInjectionContainer\Container as Container;
use bitbetrieb\MVC\HTTP\IResponse as IResponse;
use bitbetrieb\MVC\HTTP\IRequest as IRequest;

/**
 * Class Route
 * @package bitbetrieb\MVC\FrontController
 */
class Route implements IRoute {
	/**
	 * Die original Route
	 *
	 * @var string
	 */
	private $route;

	/**
	 * HTTP Methode
	 *
	 * @var string
	 */
	private $httpMethod;

	/**
	 * Callable
	 *
	 * @var string
	 */
	private $callable;

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
		$this->setHttpMethod($httpMethod);
		$this->setCallable($callable);
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
	 * Setzt Callable
	 *
	 * @param string $callable Zeichenkette in Form von "controller@funktion"
	 */
	public function setCallable($callable) {
		$this->callable = $callable;
	}

	/**
	 * Gibt Callable aus
	 *
	 * @return string
	 */
	public function getCallable() {
		return $this->callable;
	}

	/**
	 * Fügt einen Parameter für die Controllermethode hinzu
	 *
	 * @param mixed $arg
	 */
	public function addArgument($arg) {
		$this->arguments[] = $arg;
	}

	/**
	 * Überprüft ob ein Request mit der Route übereinstimmt
	 *
	 * @param IRequest $request
	 *
	 * @return bool
	 */
	public function matches(IRequest $request) {
		$matches = false;

		if ($this->httpMethodsMatch($request->method())) {
			if ($this->uriMatchesRegex($request->uri())) {
				$matches = true;
			}
		}

		return $matches;
	}

	/**
	 * Aktiviere die Route.
	 * Der Controller wird erzeugt und die Methode wird mit den notwendigen Parametern aufgerufen
	 */
	public function invoke(IRequest $request) {
		//URI Parameter an gegebene Parameter anhängen
		$this->addURIArguments($request->uri());

		//Controller Klassen Reflektor erzeugen
		$controller = new \ReflectionClass($this->getControllerClass());

		//Controller Methoden Reflektor erzeugen
		$method = new \ReflectionMethod($this->getControllerClass(), $this->getControllerMethod());

		//Controller mit Methode aufrufen und Parameter übergeben
		$method->invokeArgs($controller->newInstance(), $this->getArguments());
	}

	/**
	 * Baut die Route zu regulärem Ausdruck um und gibt diesen zurück
	 *
	 * @param string $route Route mit Platzhaltern in Form von "/diese/route/hat/{variable1}/und/{variable2}"
	 *
	 * @return string $regex Regulärer Ausdruck welcher aus der Route produziert wurde
	 */
	private function getRouteRegex() {
		//Platzhalter {} durch regulären Ausdruck (.*) ersetzen
		$regex = preg_replace("/({.*?})/", "(.*)", $this->getRoute());

		//Slashes in regulärem Ausdruck ersetzen
		$regex = str_replace('/', '\/', $regex);

		//Delimiter an regulären Ausdruck anhängen
		$regex = '/^'.$regex.'$/';

		return $regex;
	}

	/**
	 * Vergleicht HTTP Methode der Route mit übergebener Methode
	 *
	 * @param string $method Zu vergleichende HTTP Methode
	 *
	 * @return bool Enthält true wenn Methoden übereinstimmen, false wenn nicht
	 */
	private function httpMethodsMatch($method) {
		return strtoupper($method) === strtoupper($this->getHttpMethod());
	}

	/**
	 * Vergleicht URI der Route mit übergebener URI
	 *
	 * @param string $uri Zu vergleichende URI
	 *
	 * @return bool Enthält true wenn URI übereinstimmen, false wenn nicht
	 */
	private function uriMatchesRegex($uri) {
		$match = preg_match($this->getRouteRegex(), $uri);

		return ($match === 1) ? true : false;
	}

	/**
	 * Liest Parameter aus einer URI aus und fügt sie den Routen Argumenten hinzu
	 */
	private function addURIArguments($uri) {
		$params = [];

		//Parameter aus URI auslesen
		if (preg_match($this->getRouteRegex(), $uri, $params)) {
			array_shift($params);
		}

		//Parameter der Route hinzufügen
		if (count($params) > 0) {
			foreach ($params as $param) {
				$this->addArgument($param);
			}
		}
	}

	/**
	 * Gibt den Klassenname des Controllers zurück
	 *
	 * @return string Klassenname des Controllers
	 */
	private function getControllerClass() {
		return explode("@", $this->callable)[0];
	}

	/**
	 * Gibt Methodenname der am Controller aufzurufenden Methode zurück
	 *
	 * @return string Methodenname der am Controller aufzurufenden Methode
	 */
	private function getControllerMethod() {
		return explode("@", $this->callable)[1];
	}

	/**
	 * Gibt die Parameter mit denen die Controllermethode aufgerufen wird zurück
	 *
	 * @return array
	 */
	private function getArguments() {
		return $this->arguments;
	}
}

?>