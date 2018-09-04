<?php
class N2Icons {

    public static $icons = array();

    public static $keys = array();

    public static function init() {
        $path      = N2LIBRARYASSETS . '/icons/';
        $iconPacks = N2Filesystem::folders($path);

        foreach ($iconPacks AS $iconPack) {
            $manifestPath = $path . $iconPack . '/manifest.json';
            if (N2Filesystem::fileexists($manifestPath)) {
                self::$icons[$iconPack] = json_decode(N2Filesystem::readFile($manifestPath), true);
                self::$icons[$iconPack]['path'] = $path . $iconPack . '/files/' . $iconPack . '.min.css';
                self::$icons[$iconPack]['css']  = N2Uri::pathToUri($path . $iconPack . '/files/' . $iconPack . '.min.css', false);

                self::$keys[self::$icons[$iconPack]['id']] = &self::$icons[$iconPack];
            }
        }
    }

    public static function serveAdmin() {
        static $isServed = false;
        if (!$isServed) {
            N2JS::addInline('new N2Classes.Icons(' . json_encode(self::$icons) . ');');
            $isServed = true;
        }
    }

    public static function render($key) {
        $parts = explode(':', $key);
        if (count($parts) != 2) {
            return false;
        }

        $id   = $parts[0];
        $icon = $parts[1];
        if (!isset(self::$keys[$id])) {
            return false;
        }

        $iconPack = &self::$keys[$id];
        if (!isset($iconPack['data'][$icon])) {
            return false;
        }

        if (!isset($iconPack['isLoaded'])) {
            if (N2Platform::$isAdmin || N2Settings::get('icon-' . $iconPack['id'], 1)) {
                N2CSS::addFile($iconPack['path'], $iconPack['id']);
            } else if (isset($iconPack['compatibility'])) {
                N2CSS::addInline($iconPack['compatibility']);
            }
            $iconPack['isLoaded'] = true;
        }

        if ($iconPack['isLigature']) {

            return array(
                "class"    => $iconPack['class'],
                "ligature" => $icon
            );

        } else {

            return array(
                "class"    => $iconPack['class'] . " " . $iconPack['prefix'] . $icon,
                "ligature" => ""
            );
        }

    }
}

N2Icons::init();
