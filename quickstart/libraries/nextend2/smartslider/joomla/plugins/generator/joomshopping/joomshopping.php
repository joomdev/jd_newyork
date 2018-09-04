<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorJoomShopping extends N2SliderGeneratorPluginAbstract {

    protected $name = 'joomshopping';

    protected $url = 'https://extensions.joomla.org/extension/joomshopping/';

    public function getLabel() {
        return 'JoomShopping';
    }

    public function isInstalled() {
        return N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jshopping' . DIRECTORY_SEPARATOR . 'jshopping.php');
    }

    protected function loadSources() {
        new N2GeneratorJoomShoppingProducts($this, 'products', n2_('Products'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorJoomShopping);

