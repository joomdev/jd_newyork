<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorText extends N2SliderGeneratorPluginAbstract {

    protected $name = 'text';

    public function getLabel() {
        return 'CSV';
    }

    protected function loadSources() {

        new N2GeneratorTextText($this, 'text', 'CSV from url');
        new N2GeneratorTextInput($this, 'input', 'CSV from input');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorText);
