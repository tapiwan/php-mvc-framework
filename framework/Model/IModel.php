<?php

namespace bitbetrieb\MVC\Model;

/**
 * Interface IModel
 * @package bitbetrieb\MVC\Model
 */
interface IModel {
    public function __get($key);
    public function __set($key, $value);
    public function __isset($key);
    public function __unset($key);
    public static function find();
    public function save();
    public function delete();
    public function fill($modelData);
}

?>
