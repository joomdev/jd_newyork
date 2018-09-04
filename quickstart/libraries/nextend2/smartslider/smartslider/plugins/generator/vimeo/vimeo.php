<?php
N2Loader::import('libraries.settings.settings', 'smartslider');
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorVimeo extends N2SliderGeneratorPluginAbstract {

    protected $name = 'vimeo';

    protected $needConfiguration = true;

    public function getLabel() {
        return 'Vimeo';
    }

    protected function loadSources() {

        new N2GeneratorVimeoAlbum($this, 'album', 'Album');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    protected function initConfiguration() {
        static $loaded = false;
        if (!$loaded) {
            require_once dirname(__FILE__) . '/configuration.php';
            $this->configuration = new N2SliderGeneratorVimeoConfiguration($this);

            $loaded = true;
        }
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorVimeo);
