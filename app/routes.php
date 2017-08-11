<?php

/**
 * PUBLIC ROUTES
 */
$this->get('/', 'HomeController@index');

/**
 * ERROR HANDLER
 */
$this->setErrorHandler('ErrorController@index');

?>
