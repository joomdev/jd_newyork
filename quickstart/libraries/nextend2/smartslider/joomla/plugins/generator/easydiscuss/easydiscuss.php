<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorEasyDiscuss extends N2SliderGeneratorPluginAbstract {

    protected $name = 'easydiscuss';

    protected $url = 'https://extensions.joomla.org/extensions/extension/communication/question-a-answers/easydiscuss/';

    public function getLabel() {
        return 'EasyDiscuss';
    }

    public function isInstalled() {
        return N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easydiscuss');
    }

    protected function loadSources() {
        new N2GeneratorEasyDiscussDiscussions($this, 'discussions', 'Discussions');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorEasyDiscuss);

