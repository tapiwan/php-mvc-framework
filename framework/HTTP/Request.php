<?php

namespace bitbetrieb\CMS\HTTP;

class Request {
    private $uri;
    private $method;

    public function __construct() {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function uri() {
        return $this->uri;
    }

    public function method() {
        return $this->method;
    }
}

?>
