<?php

namespace bitbetrieb\MVC\HTTP;

/**
 * Class Response
 * @package bitbetrieb\MVC\HTTP
 */
class Response implements IResponse {
    /**
     * Status Code des Response
     *
     * @var int
     */
    private $status = 200;

    /**
     * Headerliste des Response
     *
     * @var array
     */
    private $headers = [];

    /**
     * Body des Response
     *
     * @var null
     */
    private $body = null;

    /**
     * Status Code des Response festlegen
     *
     * @param $code
     * @return $this
     */
    public function setStatus($code) {
        $this->status = $code;

        return $this;
    }

    /**
     * Status Code des Response zurückgeben
     *
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Header zu Response hinzufügen
     *
     * @param $key
     * @param $value
     * @return $this
     */
    public function addHeader($key, $value) {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Header aus Response löschen
     *
     * @param $key
     * @return $this
     */
    public function removeHeader($key) {
        unset($this->headers[$key]);

        return $this;
    }

    /**
     * Body des Response setzen
     *
     * @param $content
     * @return $this
     */
    public function setBody($content) {
        $this->body = $content;

        return $this;
    }

    /**
     * Body des Response erweiteren
     *
     * @param $content
     * @return $this
     */
    public function writeToBody($content) {
        $this->body .= $content;

        return $this;
    }

    /**
     * Body des Response löschen
     *
     * @return $this
     */
    public function clearBody() {
        $this->body = null;

        return $this;
    }

    /**
     * Response abschicken
     */
    public function send() {
        http_response_code($this->status);

        if(count($this->headers) >= 0) {
            foreach($this->headers as $key => $value) {
                header("$key: $value");
            }
        }

        if($this->body !== null) {
            print($this->body);
        }
    }

    /**
     * Umleiten
     */
    public function redirect($location) {
        $this->addHeader("Location", $location);

        $this->send();
    }
}

?>
