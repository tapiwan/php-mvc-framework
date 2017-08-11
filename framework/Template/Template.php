<?php

namespace bitbetrieb\CMS\Template;

use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;

class Template implements ITemplate {
    private $file;
    private $vars = [];

    public function __construct($file, $vars = []) {
        $this->file = $this->resolveFilePath($file);
        $this->vars = $vars;
    }

    public function __set($key, $value) {
        $this->set($key, $value);
    }

    public function __get($key) {
        return $this->get($key);
    }

    public function set($key, $value) {
        $this->vars[$key] = $value;
    }

    public function get($key) {
        return $this->vars[$key];
    }

    public function render() {
        if(!file_exists($this->file)) {
            throw new \Exception("Template file '{$this->file}' missing.");
        }

        ob_start();

        require($this->file);

        return ob_get_clean();
    }

    public function display() {
        echo $this->render();
    }

    public function extend($file) {
        //Variablen in Eltern-Template übernehmen
        $vars = $this->vars;

        //Content-Variable mit Content des Kind-Templates füllen
        $vars['content'] = $this->render();

        //Eltern-Template erzeugen
        $tpl = new Template($file, $vars);

        return $tpl;
    }

    public function inc($file) {
        require($this->resolveFilePath($file));
    }

    private function resolveFilePath($file) {
        return Container::get('view-directory') . $file;
    }
}

?>