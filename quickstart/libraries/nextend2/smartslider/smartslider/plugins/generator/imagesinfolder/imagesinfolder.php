<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorImagesInFolder extends N2SliderGeneratorPluginAbstract {

    protected $name = 'infolder';

    public function getLabel() {
        return n2_('Folder');
    }

    protected function loadSources() {

        new N2GeneratorInFolderImages($this, 'images', n2_('Images in folder'));

        new N2GeneratorInFolderSubFolders($this, 'subfolders', n2_('Images in folder and subfolders'));
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorImagesInFolder);
