<?php

namespace bitbetrieb\MVC\Controller;

use bitbetrieb\MVC\HTTP\Request;
use bitbetrieb\MVC\HTTP\Response ;
use bitbetrieb\MVC\Template\Template;

/**
 * Class ErrorController
 * @package bitbetrieb\MVC\Controller
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