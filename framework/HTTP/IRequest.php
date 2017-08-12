<?php

namespace bitbetrieb\CMS\HTTP;

/**
 * Interface IRequest
 * @package bitbetrieb\CMS\HTTP
 */
interface IRequest {
    public function uri();
    public function method();
    public function params();
}

?>
