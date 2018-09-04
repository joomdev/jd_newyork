<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorK2 extends N2SliderGeneratorPluginAbstract {

    protected $name = 'k2';

    protected $url = 'https://extensions.joomla.org/extension/authoring-a-content/content-construction/k2/';

    public function getLabel() {
        return 'K2';
    }

    public function isInstalled() {
        return N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_k2');
    }

    protected function loadSources() {
        new N2GeneratorK2Items($this, 'items', 'Items');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorK2);
