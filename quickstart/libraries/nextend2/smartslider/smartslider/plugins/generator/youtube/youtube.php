<?php
N2Loader::import('libraries.settings.settings', 'smartslider');
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorYoutube extends N2SliderGeneratorPluginAbstract {

    protected $name = 'youtube';

    protected $needConfiguration = true;

    public function getLabel() {
        return 'YouTube';
    }

    protected function loadSources() {

        new N2GeneratorYouTubeBySearch($this, 'bysearch', n2_('Search'));
        new N2GeneratorYouTubeByPlaylist($this, 'byplaylist', 'Playlist');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    protected function initConfiguration() {
        static $loaded = false;
        if (!$loaded) {
            require_once dirname(__FILE__) . '/configuration.php';
            $this->configuration = new N2SliderGeneratorYouTubeConfiguration($this);

            $loaded = true;
        }
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorYoutube);
