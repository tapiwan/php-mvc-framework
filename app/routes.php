<?php

/**
 * ROUTE FILE
 *
 * Here you can add your application routes. Some possibilities include:
 *
 * $this->get('/', 'HomeController@index');
 * ---> Calls the index() method of the HomeController class with Request and Response as parameters
 *
 * $this->get('/users/{name}/orders/{id}', 'HomeController@test');
 * ---> Parameters {name} and {id} are passed to the controller in addition to the Request and Response
 */

$this->get('/', 'HomeController@index');

/**
 * ERROR HANDLER
 *
 * The method that is invoked when an unknown route is called by the client
 */
$this->setErrorHandler('ErrorController@index');

?>
