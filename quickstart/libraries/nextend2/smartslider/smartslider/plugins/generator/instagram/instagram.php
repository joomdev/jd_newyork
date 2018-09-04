<?php
N2Loader::import('libraries.settings.settings', 'smartslider');

class N2SSPluginGeneratorInstagram extends N2SliderGeneratorPluginAbstract {

    protected $name = 'instagram';

    protected $needConfiguration = true;

    public function getLabel() {
        return 'Instagram';
    }

    protected function loadSources() {

        new N2GeneratorInstagramMyFeed($this, 'myfeed', n2_('My feed'));

        new N2GeneratorInstagramTagSearch($this, 'tagsearch', n2_('Search by tag'));

        new N2GeneratorInstagramMyPhotos($this, 'myphotos', n2_('My photos'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    protected function initConfiguration() {
        static $loaded = false;
        if (!$loaded) {
            require_once dirname(__FILE__) . '/configuration.php';
            $this->configuration = new N2SliderGeneratorInstagramConfiguration($this);

            $loaded = true;
        }
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorInstagram);
