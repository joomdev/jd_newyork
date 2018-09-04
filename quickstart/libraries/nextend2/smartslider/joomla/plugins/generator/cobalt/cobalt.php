<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorCobalt extends N2SliderGeneratorPluginAbstract {

    protected $name = 'cobalt';

    protected $url = 'https://extensions.joomla.org/extension/cobalt/';

    public function getLabel() {
        return 'Cobalt';
    }

    public function isInstalled() {
        return N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_cobalt');
    }

    protected function loadSources() {

        if ($this->isInstalled()) {
            $db       = new N2Model("js_res_sections");
            $sections = $db->db->queryAll("SELECT id, name FROM #__js_res_sections ORDER BY ordering ASC", array("id"), "assoc", "id");

            foreach ($sections AS $section) {
                new N2GeneratorCobaltRecords($this, 'section' . $section['id'], $section['name'], $section['id']);
            }
        } else {
            new N2GeneratorCobaltRecords($this, 'section', 'Records');
        }
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorCobalt);

