<?php

namespace bitbetrieb\CMS\Template;

use bitbetrieb\CMS\DependencyInjectionContainer\Container as Container;

/**
 * Class Template
 * @package bitbetrieb\CMS\Template
 */
class Template implements ITemplate {
	/**
	 * Name der Template-Datei
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
	 * Gerenderte Template Teile
	 *
	 * @var array
	 */
	private $renderedParts = [];

	/**
	 * Name des momentanen aktiven Blocks
	 *
	 * @var null|string
	 */
	private $currentBlock = null;

	/**
	 * Flag welche angibt ob gerade gerendert wird
	 *
	 * @var bool
	 */
	private $rendering = false;

	/**
	 * Template constructor.
	 *
	 * @param string $file Dateiname ohne Pfad, wird automatisch ergänzt
	 * @param array $vars Variablen des Templates
	 */
	public function __construct($file, $vars = []) {
		$this->file = $file;
		$this->vars = $vars;
	}

	/**
	 * Magische Get Methode für Template Variablen
	 *
	 * @param $key
	 * @return mixed
	 */
	public function __get($key) {
		return isset($this->$key) ? $this->vars[$key] : false;
	}

	/**
	 * Magische Set Methode für Template Variablen
	 *
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		//Variablen des Template sind nur veränderbar solange nicht gerendert wird
		if (!$this->rendering) {
			$this->vars[$key] = $value;
		} else {
			throw new \Exception("Can't mutate template variable '$key' during rendering process.");
		}
	}

	/**
	 * Magische Isset Methode für Template Variablen
	 *
	 * @param $key
	 * @return bool
	 */
	public function __isset($key) {
		return isset($this->vars[$key]);
	}

	/**
	 * Magische Unset Methode für Template Variable
	 *
	 * @param $key
	 */
	public function __unset($key) {
		unset($this->vars[$key]);
	}

	/**
	 * Liest die Template Datei ein und gibt den produzierten Inhalt zurück
	 *
	 * @return string Generierter Inhalt
	 * @throws \Exception TemplateNotFounds
	 */
	public function render() {
		$this->rendering = true;
		$this->startBuffer();
		$this->loadTemplateFile($this->file);
		$this->endBuffer();
		$this->rendering = false;

		return implode("", $this->renderedParts);
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
		$this->loadTemplateFile($file);
	}

	/**
	 * Lädt eine Datei in das Template
	 *
	 * @param $file
	 */
	public function load($file) {
		$this->loadTemplateFile($file);
	}

	/**
	 * Lädt die angegebene Template Datei
	 *
	 * @param $file
	 */
	private function loadTemplateFile($file) {
		$path = $this->resolveFilePath($file);

		$this->templateFileExists($path);

		require($path);
	}

	/**
	 * Überprüft ob ein Template existiert
	 */
	private function templateFileExists($file) {
		if (!file_exists($file)) {
			throw new \Exception("Template file '$file' missing.");
		}
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

	/**
	 * Startet einen Output Buffer
	 */
	private function startBuffer() {
		ob_start();
	}

	/**
	 * Speichert den bisherigen Buffer und leert ihn danach
	 *
	 * @return string
	 */
	private function interceptBuffer() {
		$this->addTemplatePart(ob_get_contents());

		ob_clean();
	}

	/**
	 * Beendet den Output Buffer
	 */
	private function endBuffer() {
		$this->interceptBuffer();

		ob_end_clean();
	}

	/**
	 * Fügt einen Template Part mit Content hinzu mit angegebenem Schlüssel oder nummeriertem Index
	 *
	 * @param $key
	 * @param $content
	 */
	private function addTemplatePart($content) {
		if (is_null($this->currentBlock)) {
			$this->renderedParts[] = $content;
		}
		else {
			$this->renderedParts[$this->currentBlock] = $content;
		}
	}

	/**
	 * Startet einen neuen benannten Block
	 *
	 * @param $name
	 */
	private function block($name) {
		$this->interceptBuffer();

		$this->currentBlock = $name;
	}

	/**
	 * Beendet einen benannten Block
	 */
	private function endblock() {
		$this->interceptBuffer();

		$this->currentBlock = null;
	}
}

?>