<?php

namespace bitbetrieb\CMS\Controller;

use bitbetrieb\CMS\HTTP\IRequest as IRequest;
use bitbetrieb\CMS\HTTP\IResponse as IResponse;
use bitbetrieb\CMS\Template\Template as Template;
use bitbetrieb\CMS\Model\User as User;

/**
 * Class HomeController
 * @package bitbetrieb\CMS\Controller
 */
class HomeController extends Controller {
    /**
     * index - Lade die Startseite
     *
     * @param IRequest $request
     * @param IResponse $response
     */
    public function index(IRequest $request, IResponse $response) {
        echo "index";
    }

    /**
     * test - Testroute
     *
     * @param IRequest $request
     * @param IResponse $response
     * @param $userName
     * @param $orderId
     */
    public function test(IRequest $request, IResponse $response, $userName, $orderId) {

        $user = User::find();

        print_r($user);

        /*$tpl = new Template('test.php', [
            "title" => "User Orders",
           "name" => $userName,
           "orderId" => $orderId
        ]);

        $tpl->extend('index.php');

        $response->setBody($tpl->render());
        $response->send();*/
    }
}

?>