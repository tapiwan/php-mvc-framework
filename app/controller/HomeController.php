<?php

namespace bitbetrieb\CMS\Controller;

class HomeController extends Controller {
    public function index($request, $response) {
        print_r(func_get_args());
    }

    public function test($request, $response, $name, $orderId) {
        print_r($request);
        print_r($response);
        echo $name;
        echo $orderId;
    }
}

?>