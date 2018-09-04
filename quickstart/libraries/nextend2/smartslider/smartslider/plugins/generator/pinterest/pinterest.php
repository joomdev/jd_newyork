<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorPinterest extends N2SliderGeneratorPluginAbstract {

    protected $name = 'pinterest';

    public function getLabel() {
        return 'Pinterest';
    }

    protected function loadSources() {

        new N2GeneratorPinterestImages($this, 'images', n2_('Images'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorPinterest);

