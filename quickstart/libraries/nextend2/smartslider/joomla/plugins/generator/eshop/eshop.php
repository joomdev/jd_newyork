<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorEShop extends N2SliderGeneratorPluginAbstract {

    protected $name = 'eshop';

    protected $url = 'https://extensions.joomla.org/extension/e-commerce/shopping-cart/eshop/';

    public function getLabel() {
        return 'EShop';
    }

    public function isInstalled() {
        return N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_eshop' . DIRECTORY_SEPARATOR . 'eshop.php');
    }

    protected function loadSources() {
        new N2GeneratorEShopProducts($this, 'products', n2_('Products'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorEShop);
