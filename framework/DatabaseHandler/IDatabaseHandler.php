<?php

namespace bitbetrieb\CMS\DatabaseHandler;

/**
 * Interface IDatabaseHandler
 * @package bitbetrieb\CMS\DatabaseHandler
 */
interface IDatabaseHandler {
    public function connect();
    public function query($query, $execution_only = false);
}

?>
