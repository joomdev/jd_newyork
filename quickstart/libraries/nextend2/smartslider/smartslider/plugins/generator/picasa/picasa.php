<?php
N2Loader::import('libraries.settings.settings', 'smartslider');
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorPicasa extends N2SliderGeneratorPluginAbstract {

    protected $name = 'picasa';

    protected $needConfiguration = true;

    public function getLabel() {
        return 'Picasa';
    }

    protected function loadSources() {

        new N2GeneratorPicasaImages($this, 'images', n2_('Images'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    protected function initConfiguration() {
        static $loaded = false;
        if (!$loaded) {
            require_once dirname(__FILE__) . '/configuration.php';
            $this->configuration = new N2SliderGeneratorPicasaConfiguration($this);

            $loaded = true;
        }
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorPicasa);
