<?php

namespace bitbetrieb\MVC\DatabaseHandler;

/**
 * Interface IQueryResult
 * @package bitbetrieb\MVC\DatabaseHandler
 */
interface IQueryResult {
    public function setSuccess($success);
    public function addData($data);
    public function setData(Array $data);
    public function setLastInsertId($lastInsertId);
    public function getSuccess();
    public function getData();
    public function getLastInsertId();
}

?>
