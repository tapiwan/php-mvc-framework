<?php

namespace bitbetrieb\CMS\Controller;

use bitbetrieb\CMS\HTTP\IRequest as IRequest;
use bitbetrieb\CMS\HTTP\IResponse as IResponse;
use bitbetrieb\CMS\Template\Template as Template;

class HomeController extends Controller {
    public function index(IRequest $request, IResponse $response) {
        $tpl = new Template('test.php', [
            "name" => "Tapiwan",
            "orderId" => "Blubbeeel"
        ]);

        $tpl = $tpl->extend('index.php');

        $response->setBody($tpl->render());
        $response->send();
    }

    public function test(IRequest $request, IResponse $response, $userName, $orderId) {
        $tpl = new Template('test.php', [
           "name" => $userName,
           "orderId" => $orderId
        ]);

        $tpl = $tpl->extend('index.php');

        $response->setBody($tpl->render());
        $response->send();
    }
}

?>