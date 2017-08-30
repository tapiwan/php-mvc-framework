<?php
/*
|--------------------------------------------------------------------------
| ROUTE DEFINITIONS
|--------------------------------------------------------------------------
|
| Here you can add your application routes. Some possibilities include:
|
| $this->get('/', 'HomeController@index');
| Calls index() method of the HomeController class with Request as parameter
|
| $this->get('/users/{name}/orders/{id}', 'HomeController@test');
| Parameters {name} and {id} are passed to the controller in addition to the Request
|
*/

$this->get('/', 'HomeController@index');



/*
|--------------------------------------------------------------------------
| ERROR ROUTE
|--------------------------------------------------------------------------
|
| The method that is invoked when an unknown route is called by the client
|
*/

$this->setErrorHandler('ErrorController@index');

?>
