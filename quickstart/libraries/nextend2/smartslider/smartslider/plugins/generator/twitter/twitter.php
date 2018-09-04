<?php
N2Loader::import('libraries.settings.settings', 'smartslider');

class N2SSPluginGeneratorTwitter extends N2SliderGeneratorPluginAbstract {

    protected $name = 'twitter';

    protected $needConfiguration = true;

    public function getLabel() {
        return 'Twitter';
    }

    protected function loadSources() {

        new N2GeneratorTwitterTimeline($this, 'timeline', 'Latest tweets');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    protected function initConfiguration() {
        static $loaded = false;
        if (!$loaded) {
            require_once dirname(__FILE__) . '/configuration.php';
            $this->configuration = new N2SliderGeneratorTwitterConfiguration($this);

            $loaded = true;
        }
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorTwitter);
