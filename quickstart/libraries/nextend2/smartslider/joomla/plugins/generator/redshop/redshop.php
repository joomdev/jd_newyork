<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorRedShop extends N2SliderGeneratorPluginAbstract {

    protected $name = 'redshop';

    protected $url = 'https://extensions.joomla.org/extensions/extension/e-commerce/shopping-cart/redshop/';

    public function getLabel() {
        return 'redSHOP';
    }

    public function isInstalled() {
        return N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_redshop' . DIRECTORY_SEPARATOR . 'redshop.php');
    }

    protected function loadSources() {
        new N2GeneratorRedShopProducts($this, 'products', n2_('Products'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorRedShop);
