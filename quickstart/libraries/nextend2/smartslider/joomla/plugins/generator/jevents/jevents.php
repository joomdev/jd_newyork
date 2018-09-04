<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorJEvents extends N2SliderGeneratorPluginAbstract {

    protected $name = 'jevents';

    protected $url = 'https://extensions.joomla.org/extension/jevents/';

    public function getLabel() {
        return 'JEvents';
    }

    public function isInstalled() {
        return N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jevents');
    }

    protected function loadSources() {
        new N2GeneratorJEventsEvents($this, 'events', 'One time events');
        new N2GeneratorJEventsRepeatingEvents($this, 'repeatingevents', 'Repeating events');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorJEvents);
