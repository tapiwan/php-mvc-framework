<?php

namespace bitbetrieb\CMS\FrontController;

use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;
use bitbetrieb\CMS\HTTP\IRequest as IRequest;

class FrontController implements IFrontController {
    /**
     * Request Object
     *
     * @var IRequest
     */
    private $request;

    /**
     * Route Sammlung
     *
     * @var array
     */
    private $routes = [];

    /**
     * FrontController constructor.
     *
     * @param IRequest $request
     * @param $routesPHP
     */
    public function __construct(IRequest $request, $routesPHP) {
        $this->request = $request;
        include($routesPHP);
    }

    /**
     * GET Route hinzufügen
     *
     * @param $route
     * @param $callable
     */
    public function get($route, $callable) {
        $this->addRoute("GET", $route, $callable);
    }

    /**
     * POST Route hinzufügen
     *
     * @param $route
     * @param $callable
     */
    public function post($route, $callable) {
        $this->addRoute("POST", $route, $callable);
    }

    /**
     * PUT Route hinzufügen
     *
     * @param $route
     * @param $callable
     */
    public function put($route, $callable) {
        $this->addRoute("PUT", $route, $callable);
    }

    /**
     * DELETE Route hinzufügen
     *
     * @param $route
     * @param $callable
     */
    public function delete($route, $callable) {
        $this->addRoute("DELETE", $route, $callable);
    }

    /**
     * Route hinzufügen
     *
     * @param $method
     * @param $route
     * @param $callable
     */
    public function addRoute($method, $route, $callable) {
        $pattern = preg_replace("/({.*?})/", "(.*)", $route);
        $pattern = '/^' . str_replace('/', '\/', $pattern) . '$/';
        $callableParts = explode("@", $callable);

        $this->routes[$pattern] = [
            "method" => $method,
            "controller" => $callableParts[0],
            "function" => $callableParts[1]
        ];
    }

    /**
     * Route ausfindig machen die zu URI des Request passt
     */
    public function execute() {
        foreach($this->routes as $pattern => $data) {
            if($data['method'] === $this->request->method()) {
                if(preg_match($pattern, $this->request->uri(), $params)) {
                    array_shift($params);

                    $controllerClass = Container::get('controller-namespace').$data['controller'];

                    $controller = new \ReflectionClass($controllerClass);
                    $method = new \ReflectionMethod($controllerClass, $data['function']);

                    $method->invokeArgs($controller->newInstance(), $params);
                }
            }
        }
    }
}

?>
