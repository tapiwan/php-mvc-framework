<?php

namespace bitbetrieb\CMS\Controller;

class HomeController extends Controller {
    public function index($request, $response) {
        print_r(func_get_args());
    }
}

?>