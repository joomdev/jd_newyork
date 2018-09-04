<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorMijoShop extends N2SliderGeneratorPluginAbstract {

    protected $name = 'mijoshop';

    protected $url = 'https://miwisoft.com/joomla-extensions/mijoshop-joomla-shopping-cart';

    public function getLabel() {
        return 'MijoShop';
    }

    public function isInstalled() {
        return N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_mijoshop' . DIRECTORY_SEPARATOR . 'mijoshop.php');
    }

    protected function loadSources() {
        new N2GeneratorMijoShopProducts($this, 'products', n2_('Products'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorMijoShop);
