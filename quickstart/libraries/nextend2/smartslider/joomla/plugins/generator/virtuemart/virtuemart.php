<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorVirtueMart extends N2SliderGeneratorPluginAbstract {

    protected $name = 'virtuemart';

    protected $url = 'https://extensions.joomla.org/extension/virtuemart/';

    public function getLabel() {
        return 'VirtueMart';
    }

    public function isInstalled() {
        return N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
    }

    protected function loadSources() {
        new N2GeneratorVirtueMartProducts($this, 'products', n2_('Products'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorVirtueMart);
