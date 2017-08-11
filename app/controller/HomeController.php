<?php

namespace bitbetrieb\CMS\Controller;

use bitbetrieb\CMS\HTTP\IRequest as IRequest;
use bitbetrieb\CMS\HTTP\IResponse as IResponse;
use bitbetrieb\CMS\Template\Template as Template;

class HomeController extends Controller {
    public function index(IRequest $request, IResponse $response) {
        $tpl = new Template('test.php', [
            "name" => "Tapiwan",
            "blub" => "Blubbeeel",
            "friends" => ["Test", "Blub", "Wtf"]
        ]);

        $layout = new Template('index.php', [
           'content' => $tpl->render()
        ]);

        $response->setBody($layout->render());
        $response->send();
    }
}

?>