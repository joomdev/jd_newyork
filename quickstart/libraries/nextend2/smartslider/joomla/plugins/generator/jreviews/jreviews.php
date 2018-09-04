<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorJReviews extends N2SliderGeneratorPluginAbstract {

    protected $name = 'jreviews';

    protected $url = 'https://www.jreviews.com/joomla';

    public function getLabel() {
        return 'JReviews';
    }

    public function isInstalled() {
        return N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jreviews');
    }

    protected function loadSources() {
        new N2GeneratorJReviewsComments($this, 'comments', n2_('Comments'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorJReviews);
