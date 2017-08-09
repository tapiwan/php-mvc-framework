<?php

include realpath("../framework/Autoloader/Autoloader.php");
$autoloader = new bitbetrieb\CMS\Autoloader\Autoloader();
$autoloader->initializeNamespacesFromJSON("../config/autoload.json");
$autoloader->register();

$container = new bitbetrieb\CMS\ServiceContainer\Container();
$container->initializeServicesFromJSON("../config/services.json");
$container->get('database-handler');

print_r($container);


?>
