<?php

namespace bitbetrieb\MVC\Template;

use bitbetrieb\MVC\DependencyInjectionContainer\Container as Container;

/**
 * Class Template
 * @package bitbetrieb\MVC\Template
 */
class Template implements ITemplate {
    /**
     * Verzeichnis in dem alle Template Dateien liegen
     *
     * @var string
     */
    private static $viewDirectory = "";

    /**
     * Verzeichnis in dem dieses Template liegt
     */
    private $fileDirectory = "";

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
	private $parts = [];

	/**
	 * Name des momentanen aktiven Blocks
     *
     * @var string
	 */
	private $currentBlock = "";

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
	public function __construct($file, $vars = [], $fileDirectory = "") {
		$this->file = $file;
		$this->vars = $vars;
		$this->fileDirectory = $fileDirectory;
	}

	/**
	 * Magische Get Methode für Template Variablen
	 *
	 * @param $key
	 * @return mixed
	 */
	public function __get($key) {
		return $this->vars[$key];
	}

	/**
	 * Set Methode für Template Variablen
	 *
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		//Variablen des Template sind nur veränderbar solange nicht gerendert wird
		if (!$this->rendering) {
			$this->vars[$key] = $value;
		} else {
			throw new \Exception("Can't mutate template variables '$key' during rendering process.");
		}
	}

    /**
     * Setzt das Verzeichnis in dem alle Templates liegen
     *
     * @param $dir
     */
	public static function setViewDirectory($dir) {
	    self::$viewDirectory = $dir;
    }

    /**
     * Setzt das Verzeichnis in dem dieses Template liegt
     *
     * @param $dir
     */
    public function setFileDirectory($dir) {
	    $this->fileDirectory = $dir;
    }

	/**
	 * Startet einen neuen Block
	 *
	 * @param $name
	 */
	private function block($name) {
		$this->interceptBuffer();

		$this->currentBlock = $name;
	}

	/**
	 * Beendet einen Block
	 */
	private function endblock() {
		$this->interceptBuffer($this->currentBlock);

		$this->currentBlock = "";
	}

	/**
	 * Liest die Template Datei ein und gibt den produzierten Inhalt zurück
	 *
	 * @return string Generierter Inhalt
	 * @throws \Exception TemplateNotFound
	 */
	public function render() {
		$this->rendering = true;
		$this->startBuffer();
		$this->loadTemplateFile($this->file);
		$this->endBuffer();
		$this->rendering = false;

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
	    if(!empty($this->fileDirectory)) {
	        return $this->fileDirectory.DIRECTORY_SEPARATOR.$file;
        }
        else if(!empty(self::$viewDirectory)) {
	        return self::$viewDirectory.DIRECTORY_SEPARATOR.$file;
        }

		return $file;
	}

	/**
	 * Startet einen Output Buffer
	 */
	private function startBuffer() {
		ob_start();
	}

	/**
	 * Speichert den bisherigen Buffer und leert ihn danach
	 */
	private function interceptBuffer($key = "") {
		$this->addTemplatePart($key, ob_get_contents());

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
	private function addTemplatePart($key, $content) {
		if (empty($key)) {
			$this->parts[] = $content;
		} else {
			$this->parts[$key] = $content;
		}
	}
}

?>