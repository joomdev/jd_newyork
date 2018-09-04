<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorIgniteGallery extends N2SliderGeneratorPluginAbstract {

    protected $name = 'ignitegallery';

    protected $url = 'https://extensions.joomla.org/profile/extension/photos-a-images/galleries/ignite-gallery/';

    public function getLabel() {
        return 'Ignite Gallery';
    }

    public function isInstalled() {
        return N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_igallery');
    }

    protected function loadSources() {
        new N2GeneratorIgniteGalleryImages($this, 'images', n2_('Images'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorIgniteGallery);

