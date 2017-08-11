<?php

namespace bitbetrieb\CMS\Model;

abstract class Model {
    protected $table;
    protected $fillable = [];

    public static function find($criteria) {
        echo 'Looking up criteria'.$criteria;
    }

    protected function save() {
        echo 'Saving';
    }
}

?>
