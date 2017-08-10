<?php

namespace bitbetrieb\CMS\HTTP;

interface IRequest {
    public function uri();
    public function method();
    public function params();
}

?>
