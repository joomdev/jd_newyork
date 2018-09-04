<?php
N2Loader::import('libraries.settings.settings', 'smartslider');
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorEcwid extends N2SliderGeneratorPluginAbstract {

    protected $name = 'ecwid';

    protected $needConfiguration = true;

    public function getLabel() {
        return 'Ecwid';
    }

    protected function loadSources() {

        new N2GeneratorEcwidProducts($this, 'products', n2_('Products'));
        new N2GeneratorEcwidRandom_Products($this, 'random_products', n2_('Random products'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    protected function initConfiguration() {
        static $loaded = false;
        if (!$loaded) {
            require_once dirname(__FILE__) . '/configuration.php';
            $this->configuration = new N2SliderGeneratorEcwidConfiguration($this);

            $loaded = true;
        }
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorEcwid);



