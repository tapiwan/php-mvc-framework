<?php

namespace bitbetrieb\MVC\DatabaseHandler;

/**
 * Interface IDatabaseHandler
 * @package bitbetrieb\MVC\DatabaseHandler
 */
interface IDatabaseHandler {
    public function __construct($host, $user, $db, $password);
    public function connect();
    public function query($query);
}

?>
