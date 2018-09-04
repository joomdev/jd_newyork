<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorEasyBlog extends N2SliderGeneratorPluginAbstract {

    protected $name = 'easyblog';

    protected $url = 'https://extensions.joomla.org/extension/easyblog/';

    public function getLabel() {
        return 'EasyBlog';
    }

    public function isInstalled() {
        return N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog');
    }

    protected function loadSources() {
        new N2GeneratorEasyBlogPosts($this, 'posts', 'Posts');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorEasyBlog);

