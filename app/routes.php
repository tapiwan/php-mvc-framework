<?php

/**
 * PUBLIC ROUTES
 */
$this->get('/', 'HomeController@index');
$this->get('/users/{name}/orders/{id}', 'HomeController@test');

/**
 * ERROR HANDLER
 */
$this->setErrorHandler('ErrorController@index');

?>
