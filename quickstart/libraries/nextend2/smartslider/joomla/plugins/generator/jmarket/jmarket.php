<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorJMarket extends N2SliderGeneratorPluginAbstract {

    protected $name = 'jmarket';

    protected $url = 'https://extensions.joomla.org/extension/jmarket/';

    public function getLabel() {
        return 'JMarket';
    }

    public function isInstalled() {
        return N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jmarket' . DIRECTORY_SEPARATOR . 'jmarket.php');
    }

    protected function loadSources() {
        new N2GeneratorJMarketProducts($this, 'products', n2_('Products'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorJMarket);
