<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorJAuction extends N2SliderGeneratorPluginAbstract {

    protected $name = 'jauction';

    protected $url = 'https://joobi.co/jauction.html';

    public function getLabel() {
        return 'JAuction';
    }

    public function isInstalled() {
        return N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jauction' . DIRECTORY_SEPARATOR . 'jauction.php');
    }

    protected function loadSources() {
        new N2GeneratorJAuctionAuctions($this, 'auctions', 'Auctions');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorJAuction);

