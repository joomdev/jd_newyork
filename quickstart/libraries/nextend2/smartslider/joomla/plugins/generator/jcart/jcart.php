<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorJCart extends N2SliderGeneratorPluginAbstract {

    protected $name = 'jcart';

    protected $url = 'https://extensions.joomla.org/extension/jcart-for-opencart/';

    public function getLabel() {
        return 'JCart';
    }

    public function isInstalled() {
        return N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jcart' . DIRECTORY_SEPARATOR . 'jcart.php');
    }

    protected function loadSources() {
        new N2GeneratorJCartProducts($this, 'products', n2_('Products'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorJCart);
