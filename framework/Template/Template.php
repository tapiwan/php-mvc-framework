<?php

namespace bitbetrieb\CMS\Template;

use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;

/**
 * Class Template
 * @package bitbetrieb\CMS\Template
 */
class Template implements ITemplate {
    /**
     * Pfad zur Template-Datei
     *
     * @var string
     */
    private $file;

    /**
     * Template Variablen
     *
     * @var array
     */
    private $vars = [];

    /**
     * Template constructor.
     *
     * @param string $file Dateiname ohne Pfad, wird automatisch ergänzt
     * @param array $vars Variablen des Templates
     */
    public function __construct($file, $vars = []) {
        $this->setFile($file);
        $this->vars = $vars;
    }

    /**
     * Magische Set Methode für Template Variablen
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value) {
        $this->set($key, $value);
    }

    /**
     * Magische Get Methode für Template Variablen
     *
     * @param $key
     * @return mixed
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Set Methode für Template Variablen
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value) {
        $this->vars[$key] = $value;
    }

    /**
     * Get Methode für Template Variablen
     *
     * @param $key
     * @return mixed
     */
    public function get($key) {
        return $this->vars[$key];
    }

    /**
     * Liest die Template Datei ein und gibt den produzierten Inhalt zurück
     *
     * @return string Generierter Inhalt
     * @throws \Exception TemplateNotFound
     */
    public function render() {
        if(!file_exists($this->file)) {
            throw new \Exception("Template file '{$this->file}' missing.");
        }

        ob_start();

        require($this->file);

        return ob_get_clean();
    }

    /**
     * Liest die Template Datei ein und zeigt den produzierten Inhalt an
     *
     * @return void
     */
    public function display() {
        echo $this->render();
    }

    /**
     * Erzeugt ein Eltern-Template mit den Variablen des Kind-Templates plus dem Content des generierten Kind-Templates
     *
     * @param $file
     */
    public function extend($file) {
        //Variablen in Eltern-Template übernehmen
        $this->set('content', $this->render());
        $this->setFile($file);
    }

    /**
     * Lädt eine Datei in das Template
     *
     * @param $file
     */
    public function inc($file) {
        require($this->resolveFilePath($file));
    }

    /**
     * Ändert die Template Datei
     *
     * @param $file
     */
    private function setFile($file) {
        $this->file = $this->resolveFilePath($file);
    }

    /**
     * Gibt den Pfad zu einer Datei mit dem Views Verzeichnis zurück
     *
     * @param $file
     * @return string
     */
    private function resolveFilePath($file) {
        return Container::get('view-directory') . $file;
    }
}

?>