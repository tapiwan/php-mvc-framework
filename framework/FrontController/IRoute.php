<?php

namespace bitbetrieb\MVC\FrontController;

use bitbetrieb\MVC\HTTP\IResponse as IResponse;
use bitbetrieb\MVC\HTTP\IRequest as IRequest;

/**
 * Interface IRoute
 * @package bitbetrieb\MVC\FrontController
 */
interface IRoute {
	public function __construct($route, $httpMethod, $callable);
	public function addArgument($arg);
	public function matches(IRequest $request);
	public function invoke(IRequest $request);
}

?>