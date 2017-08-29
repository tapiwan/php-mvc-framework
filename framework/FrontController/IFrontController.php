<?php

namespace bitbetrieb\MVC\FrontController;

use bitbetrieb\MVC\HTTP\IResponse as IResponse;
use bitbetrieb\MVC\HTTP\IRequest as IRequest;

/**
 * Interface IFrontController
 * @package bitbetrieb\MVC\FrontController
 */
interface IFrontController {
    public function __construct(IRequest $request);
    public function addRoute($method, $route, $callable);
    public function setErrorHandler($callable);
    public function execute();
}

?>
