<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorZoo extends N2SliderGeneratorPluginAbstract {

    protected $name = 'zoo';

    protected $url = 'https://extensions.joomla.org/extension/zoo/';

    public function getLabel() {
        return 'Zoo';
    }

    public function isInstalled() {
        return N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_zoo');
    }

    protected function loadSources() {
        require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_zoo' . DIRECTORY_SEPARATOR . 'config.php');
        $zoo = App::getInstance('zoo');

        $apps = $zoo->table->application->all(array('order' => 'name'));

		if(is_array($apps)){
			foreach ($apps AS $app) {
				foreach ($app->getTypes() AS $type) {
					//Make them class name safe
					$appId      = preg_replace('/[^a-zA-Z0-9_\x7f-\xff]*/', '', $app->id);
					$identifier = preg_replace('/[^a-zA-Z0-9_\x7f-\xff]*/', '', $type->identifier);

					new N2GeneratorZooItems($this, $appId . $identifier, $app->name . ' (' . $identifier . ')', $app->id, $type->identifier);
				}
			}
		}
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorZoo);
