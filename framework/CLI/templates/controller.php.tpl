<?php

namespace bitbetrieb\CMS\Controller;

use bitbetrieb\CMS\HTTP\IRequest as IRequest;
use bitbetrieb\CMS\HTTP\IResponse as IResponse;
use bitbetrieb\CMS\Template\Template as Template;

/**
 * Class {ControllerName}
 * @package bitbetrieb\CMS\Controller
 */
class {ControllerName} extends Controller {
    /**
     * Index
     *
     * @param IRequest $request
     * @param IResponse $response
     */
    public function index(IRequest $request, IResponse $response) {
        echo "Index";
    }
}

?>