<?php

namespace bitbetrieb\CMS\Model;

class User extends Model {
    protected $fillable = [
      'id' => 'int',
      'name' => 'string'
    ];
}

?>