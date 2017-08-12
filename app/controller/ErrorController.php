<?php

namespace bitbetrieb\CMS\Controller;

/**
 * Class ErrorController
 * @package bitbetrieb\CMS\Controller
 */
class ErrorController extends Controller {
    public function index($request, $response) {
        echo "Error!";
    }
}

?>