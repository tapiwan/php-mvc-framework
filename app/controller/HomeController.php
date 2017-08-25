<?php

namespace bitbetrieb\MVC\Controller;

use bitbetrieb\MVC\HTTP\Request as Request;
use bitbetrieb\MVC\HTTP\Response as Response;
use bitbetrieb\MVC\Template\Template as Template;

/**
 * Class HomeController
 * @package bitbetrieb\MVC\Controller
 */
class HomeController extends Controller {
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