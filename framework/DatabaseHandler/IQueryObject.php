<?php

namespace bitbetrieb\MVC\DatabaseHandler;

interface IQueryObject {
    public function selectFrom($columns, $tables);
    public function insertInto($tables, $data);
    public function update($tables, $data);
    public function deleteFrom($tables);
    public function where($key, $operator, $value);
    public function _and($key, $operator, $value);
    public function _or($key, $operator, $value);
    public function addCriterion($cmd, $key, $operator, $value);
    public function addCriteria(Array $criteria);
    public function limit($amount);
    public function assemble();
}

?>