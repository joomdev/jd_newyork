<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorHikaShop extends N2SliderGeneratorPluginAbstract {

    protected $name = 'hikashop';

    protected $url = 'https://extensions.joomla.org/extension/hikashop/';

    public function getLabel() {
        return 'HikaShop';
    }

    public function isInstalled() {
        return N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_hikashop' . DIRECTORY_SEPARATOR . 'hikashop.php');
    }

    protected function loadSources() {
        new N2GeneratorHikaShopProducts($this, 'products', n2_('Products'));
        new N2GeneratorHikaShopProductsbyid($this, 'productsbyid', n2_('Products') . ' - IDs');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorHikaShop);
