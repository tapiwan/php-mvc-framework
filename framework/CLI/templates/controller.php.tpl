<?php

namespace bitbetrieb\CMS\Controller;

use bitbetrieb\CMS\HTTP\Request as Request;
use bitbetrieb\CMS\HTTP\Response as Response;
use bitbetrieb\CMS\Template\Template as Template;

/**
 * Class {ControllerName}
 * @package bitbetrieb\CMS\Controller
 */
class {ControllerName} extends Controller {
    /**
     * Index
     *
     * @param Request $req
     */
    public function index(Request $req) {
        $tpl = new Template('index.php', [
        	   "title" => "Home"
        ]);

        $res = new Response();
        $res->setBody($tpl->render())
            ->send();
    }
}

?>