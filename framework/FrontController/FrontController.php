<?php

namespace bitbetrieb\CMS\FrontController;

class FrontController implements IFrontController {
    private $request;

    public function __construct($request) {
        $this->request = $request;
    }

    public function handleRequest() {

    }
}

?>
