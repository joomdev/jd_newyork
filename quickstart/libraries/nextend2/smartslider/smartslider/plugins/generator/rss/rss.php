<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorRSS extends N2SliderGeneratorPluginAbstract {

    protected $name = 'rss';

    public function getLabel() {
        return 'RSS';
    }

    protected function loadSources() {

        new N2GeneratorRSSFeed($this, 'feed', 'RSS Feed');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorRSS);
