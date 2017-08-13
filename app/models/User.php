<?php

namespace bitbetrieb\CMS\Model;

class User extends Model {
    public function __construct($data = null) {
        parent::__construct($data);

        $this->fillable = [
            'name',
            'lastname',
            'age'
        ];
    }
}

?>