<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorRSEventsPro extends N2SliderGeneratorPluginAbstract {

    protected $name = 'rseventspro';

    protected $url = 'https://extensions.joomla.org/extension/rsevents-pro/';

    public function getLabel() {
        return 'RSEvents!Pro';
    }

    public function isInstalled() {
        return N2Filesystem::existsFile(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_rseventspro' . DIRECTORY_SEPARATOR . 'rseventspro.php');
    }

    protected function loadSources() {
        new N2GeneratorRSEventsProEvents($this, 'events', n2_('Events'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorRSEventsPro);
