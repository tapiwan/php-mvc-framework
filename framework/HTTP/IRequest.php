<?php

namespace bitbetrieb\MVC\HTTP;

/**
 * Interface IRequest
 * @package bitbetrieb\MVC\HTTP
 */
interface IRequest {
    public function uri();
    public function method();
    public function params();
}

?>
