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
     * @param Request $req
     */
    public function index(Request $req) {
        $tpl = new Template('error.php', [
            "title" => "Error"
        ]);

        $res = new Response();
        $res->setStatus(404)
            ->setBody($tpl->render())
            ->send();
    }
}

?>