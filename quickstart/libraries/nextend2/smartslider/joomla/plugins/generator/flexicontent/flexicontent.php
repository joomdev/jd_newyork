<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorFlexiContent extends N2SliderGeneratorPluginAbstract {

    protected $name = 'flexicontent';

    protected $url = 'https://extensions.joomla.org/extension/flexicontent/';

    public function getLabel() {
        return 'FLEXIcontent';
    }

    public function isInstalled() {
        return N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_flexicontent');
    }

    protected function loadSources() {
        new N2GeneratorFlexiContentItems($this, 'items', 'Items');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorFlexiContent);

