<?php

namespace bitbetrieb\CMS\Controller;

use bitbetrieb\CMS\HTTP\IRequest as IRequest;
use bitbetrieb\CMS\HTTP\IResponse as IResponse;
use bitbetrieb\CMS\Template\Template as Template;

/**
 * Class ErrorController
 * @package bitbetrieb\CMS\Controller
 */
class ErrorController extends Controller {
    /**
     * Index
     *
     * @param IRequest $request
     * @param IResponse $response
     */
    public function index(IRequest $request, IResponse $response) {
        $response->setStatus(404);
        $response->setBody("Error");
        $response->send();
    }
}

?>