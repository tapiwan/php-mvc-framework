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
        echo "Index";
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
        /*$user = new User();

        $user->id = 25;
        $user->name = 'Test';
        $user->lastname = "ASd";
        $user->age = 20;

        $user->delete();*/

        $user = User::find(
            ['where', 'id', '<=', 74]
        );

        print_r($user);
    }
}

?>