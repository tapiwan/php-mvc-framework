<?php

namespace bitbetrieb\MVC\HTTP;

/**
 * Interface IResponse
 * @package bitbetrieb\MVC\HTTP
 */
interface IResponse {
    public function setStatus($code);
    public function getStatus();
    public function addHeader($key, $value);
    public function removeHeader($key);
    public function setBody($content);
    public function writeToBody($content);
    public function clearBody();
    public function send();
    public function redirect($location);
}

?>
