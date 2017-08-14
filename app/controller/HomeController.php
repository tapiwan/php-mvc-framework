<?php

namespace bitbetrieb\CMS\Controller;

use bitbetrieb\CMS\HTTP\IRequest as IRequest;
use bitbetrieb\CMS\HTTP\IResponse as IResponse;
use bitbetrieb\CMS\Template\Template as Template;

/**
 * Class HomeController
 * @package bitbetrieb\CMS\Controller
 */
class HomeController extends Controller {
    /**
     * Index
     *
     * @param IRequest $request
     * @param IResponse $response
     */
    public function index(IRequest $request, IResponse $response) {
        $tpl = new Template('b.php', [
        	   "name" => "Dirk",
            "age" => 23,
            "friends" => ["Max", "Peter", "Luis"]
        ]);

        $response->setBody($tpl->render());
        $response->send();
    }
}

?>