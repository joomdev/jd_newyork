<?php
N2Loader::import('libraries.settings.settings', 'smartslider');

class N2SSPluginGeneratorDribbble extends N2SliderGeneratorPluginAbstract {

    protected $name = 'dribbble';

    protected $needConfiguration = true;

    public function getLabel() {
        return 'Dribbble';
    }

    protected function loadSources() {

        new N2GeneratorDribbbleShots($this, 'shots', 'Shots');
        new N2GeneratorDribbbleProject($this, 'project', 'Project');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    protected function initConfiguration() {
        static $loaded = false;
        if (!$loaded) {
            require_once dirname(__FILE__) . '/configuration.php';
            $this->configuration = new N2SliderGeneratorDribbbleConfiguration($this);

            $loaded = true;
        }
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorDribbble);
