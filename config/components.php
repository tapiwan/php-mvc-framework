<?php

use bitbetrieb\MVC\Config\Config;
use bitbetrieb\MVC\DependencyInjectionContainer\Container;

//Front Controller
Container::addSingleton("front-controller", "bitbetrieb\\MVC\\FrontController\\FrontController", [
	"request"
]);

//Request
Container::addSingleton("request", "bitbetrieb\\MVC\\HTTP\\Request", []);

//Database Handler
Container::addSingleton("database-handler", "bitbetrieb\\MVC\\DatabaseHandler\\DatabaseHandler", [
	Config::get('database/host'),
	Config::get('database/user'),
	Config::get('database/name'),
	Config::get('database/password')
]);

?>