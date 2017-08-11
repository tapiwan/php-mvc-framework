<?php

namespace bitbetrieb\CMS\Model;

use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;

abstract class Model {
    protected $class;
    protected $table;
    protected $data = [];
    protected $fillable = [
        'id' => 'int'
    ];
    protected $hidden = [];
    protected $dbh;

    public function __construct() {
        $this->class = get_class($this);
        $this->table = strtolower(array_pop(explode("\\", $this->class)))."s";
        $this->dbh = Container::get('database-handler');

        print_r($this);
    }

    public function __get($key) {
        return $this->$key;
    }

    public function __set($key, $value) {
        $this->$key = $value;
    }

    public static function find($criteria) {
        Container::get('database-handler')->query(self::buildFindQuery($criteria));
    }

    protected function save() {
        $this->dbh->query($this->buildSaveQuery());
    }

    protected function delete() {
        $this->dbh->query($this->buildDeleteQuery());
    }

    protected function update() {
        $this->dbh->query($this->buildUpdateQuery());
    }

    private static function buildFindQuery($criteria) {
        return "FIND";
    }

    private function buildSaveQuery() {
        return "SAVE";
    }

    private function buildDeleteQuery() {
        return "DELETE";
    }

    private function buildUpdateQuery() {
        return "UPDATE";
    }
}

?>
