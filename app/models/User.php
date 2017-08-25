<?php

namespace bitbetrieb\MVC\Model;

class User extends Model {
    public function __construct() {
        parent::__construct();

        $this->fillable = [
            'name'
        ];
    }
}

?>