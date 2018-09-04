<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorJomSocial extends N2SliderGeneratorPluginAbstract {

    protected $name = 'jomsocial';

    protected $url = 'https://www.jomsocial.com/';

    public function getLabel() {
        return 'JomSocial';
    }

    public function isInstalled() {
        return N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community');
    }

    protected function loadSources() {
        new N2GeneratorJomSocialEvents($this, 'events', n2_('Events'));
        new N2GeneratorJomSocialGroups($this, 'groups', n2_('Groups'));
        new N2GeneratorJomSocialVideos($this, 'videos', n2_('Videos'));
        new N2GeneratorJomSocialActivities($this, 'activities', 'Activities');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorJomSocial);

