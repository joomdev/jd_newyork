<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorPhocaGallery extends N2SliderGeneratorPluginAbstract {

    protected $name = 'phocagallery';

    protected $url = 'https://extensions.joomla.org/extension/phoca-gallery/';

    public function getLabel() {
        return 'Phoca Gallery';
    }

    public function isInstalled() {
        return N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_phocagallery');
    }

    protected function loadSources() {
        new N2GeneratorPhocaGalleryImages($this, 'images', n2_('Images'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorPhocaGallery);
