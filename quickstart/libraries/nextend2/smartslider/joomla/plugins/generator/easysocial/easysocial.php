<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorEasySocial extends N2SliderGeneratorPluginAbstract {

    protected $name = 'easysocial';

    protected $url = 'https://extensions.joomla.org/extension/easysocial/';

    public function getLabel() {
        return 'EasySocial';
    }

    public function isInstalled() {
        return N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysocial');
    }

    protected function loadSources() {
        new N2GeneratorEasySocialEvents($this, 'events', n2_('Events'));
        new N2GeneratorEasySocialGroups($this, 'groups', n2_('Groups'));
        new N2GeneratorEasySocialAlbums($this, 'albums', n2_('Albums'));
        new N2GeneratorEasySocialVideos($this, 'videos', n2_('Videos'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorEasySocial);

