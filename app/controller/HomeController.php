<?php

namespace bitbetrieb\CMS\Controller;

use bitbetrieb\CMS\HTTP\IRequest as IRequest;
use bitbetrieb\CMS\HTTP\IResponse as IResponse;
use bitbetrieb\CMS\Template\Template as Template;
use bitbetrieb\CMS\Model\User as User;

class HomeController extends Controller {
    public function index(IRequest $request, IResponse $response) {
        $tpl = new Template('test.php', [
            "title" => "Testtitel",
            "name" => "Tapiwan",
            "orderId" => "Blubbeeel"
        ]);

        $tpl = $tpl->extend('index.php');

        $response->setBody($tpl->render());
        $response->send();
    }

    public function test(IRequest $request, IResponse $response, $userName, $orderId) {
        $tpl = new Template('test.php', [
            "title" => "User Orders",
           "name" => $userName,
           "orderId" => $orderId
        ]);

        $tpl = $tpl->extend('index.php');

        $response->setBody($tpl->render());
        $response->send();
    }
}

?>