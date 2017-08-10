<?php

namespace bitbetrieb\CMS\PathFinder;

class PathFinder {
    public function document_root() {
        return $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR;
    }

    public function base_dir() {
        return dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR;
    }

    public function dir($path = "") {
        $path = str_replace("/", DIRECTORY_SEPARATOR, $path);
        $path = str_replace("\\", DIRECTORY_SEPARATOR, $path);

        return $this->base_dir().$path;
    }
}

?>