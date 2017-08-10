<?php

namespace bitbetrieb\CMS\HTTP;

class Request implements IRequest {
    /**
     * URI des Request
     *
     * @var
     */
    private $uri;

    /**
     * Methode des Request
     *
     * @var
     */
    private $method;

    /**
     * GET und POST Parameter des Request
     *
     * @var object
     */
    private $params;

    /**
     * Request constructor.
     */
    public function __construct() {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->params = (object) $_REQUEST;
    }

    /**
     * URI des Request zurückgeben
     *
     * @return mixed
     */
    public function uri() {
        return $this->uri;
    }

    /**
     * Methode des Request zurückgeben
     *
     * @return mixed
     */
    public function method() {
        return $this->method;
    }

    /**
     * GET und POST Parameter des Request zurückgeben
     *
     * @return object
     */
    public function params() {
        return $this->params;
    }
}

?>
