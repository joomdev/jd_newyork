<?php
N2Loader::import('libraries.settings.settings', 'smartslider');

class N2SSPluginGeneratorFlickr extends N2SliderGeneratorPluginAbstract {

    protected $name = 'flickr';

    protected $needConfiguration = true;

    public function getLabel() {
        return 'Flickr';
    }

    protected function loadSources() {

        new N2GeneratorFlickrPeoplePhotoStream($this, 'peoplephotostream', 'Photostream');

        new N2GeneratorFlickrPeopleAlbum($this, 'peoplealbum', 'Album');

        new N2GeneratorFlickrPeoplePhotoGallery($this, 'peoplephotogallery', 'Photogallery');

        new N2GeneratorFlickrPhotosSearch($this, 'photossearch', n2_('Search'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    protected function initConfiguration() {
        static $loaded = false;
        if (!$loaded) {
            require_once dirname(__FILE__) . '/configuration.php';
            $this->configuration = new N2SliderGeneratorFlickrConfiguration($this);

            $loaded = true;
        }
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorFlickr);
