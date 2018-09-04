<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorDJClassifieds extends N2SliderGeneratorPluginAbstract {

	protected $name = 'djclassifieds';

	protected $url = 'https://extensions.joomla.org/extension/dj-classifieds/';

	public function getLabel() {
		return 'DJ Classifieds';
	}

	public function isInstalled() {
		return N2Filesystem::existsFolder(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_djclassifieds');
	}

	protected function loadSources() {
		new N2GeneratorDJClassifiedsItems($this, 'items', n2_('Items'));
	}

	public function getPath() {
		return dirname(__FILE__) . DIRECTORY_SEPARATOR;
	}
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorDJClassifieds);

