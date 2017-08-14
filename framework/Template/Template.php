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
	 * Rendered template parts
	 *
	 * @var array
	 */
	private $parts = [];

	/**
	 * Name des momentanen Blocks
	 */
	private $currentBlock = "";

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
	 * Startet einen Output Buffer
	 */
	private function startBuffer() {
		ob_start();
	}

	/**
	 * Gibt den bisherigen Output Buffer zurück
	 *
	 * @return string
	 */
	private function interceptBuffer($key = "") {
		if(empty($key)) {
			$this->parts[] = ob_get_contents();
		}
		else {
			$this->parts[$key] = ob_get_contents();
		}

		ob_clean();
	}

	private function endBuffer() {
		$this->interceptBuffer();

		ob_end_clean();
	}

	private function block($name) {
		$this->interceptBuffer();

		$this->currentBlock = $name;
	}

	private function endblock() {
		$this->interceptBuffer($this->currentBlock);
	}

	/**
	 * Liest die Template Datei ein und gibt den produzierten Inhalt zurück
	 *
	 * @return string Generierter Inhalt
	 * @throws \Exception TemplateNotFound
	 */
	public function render() {
		if (!file_exists($this->file)) {
			throw new \Exception("Template file '{$this->file}' missing.");
		}

		$this->startBuffer();

		require($this->file);

		$this->endBuffer();

		return implode($this->parts);
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
		require($this->resolveFilePath($file));
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
		return Container::get('view-directory').$file;
	}
}

?>