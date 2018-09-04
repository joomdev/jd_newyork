<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorODude extends N2SliderGeneratorPluginAbstract {

    protected $name = 'odude';

    protected $url = 'https://extensions.joomla.org/extensions/extension/photos-a-images/ecards/odude-ecards/';

    public function getLabel() {
        return 'ODude';
    }

    public function isInstalled() {
        return N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_odudecard');
    }

    protected function loadSources() {
        new N2GeneratorODudeECard($this, 'ecard', 'E-card');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorODude);
