<?php

namespace bitbetrieb\CMS\Controller;

use bitbetrieb\CMS\HTTP\Request as Request;
use bitbetrieb\CMS\HTTP\Response as Response;
use bitbetrieb\CMS\Template\Template as Template;

/**
 * Class ErrorController
 * @package bitbetrieb\CMS\Controller
 */
class ErrorController extends Controller {
    /**
     * Index
     *
     * @param Request $request
     */
    public function index(Request $req) {
        $response = new Response();
        $response->setStatus(404);
        $response->setBody("Error");
        $response->send();
    }
}

?>