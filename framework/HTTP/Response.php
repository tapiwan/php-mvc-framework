<?php

namespace bitbetrieb\CMS\HTTP;

class Response {
    private $status = 200;
    private $headers = [];
    private $body = null;

    public function setStatus($code) {
        $this->status = $code;

        return $this;
    }

    public function getStatus() {
        return $this->status;
    }

    public function addHeader($key, $value) {
        $this->headers[$key] = $value;

        return $this;
    }

    public function removeHeader($key) {
        unset($this->headers[$key]);

        return $this;
    }

    public function setBody($content) {
        $this->body = $content;

        return $this;
    }

    public function writeToBody($content) {
        $this->body .= $content;

        return $this;
    }

    public function clearBody() {
        $this->body = null;

        return $this;
    }

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
}

?>
