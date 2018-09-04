<?php
if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
    N2Loader::import('libraries.settings.settings', 'smartslider');
    N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

    class N2SSPluginGeneratorFacebook extends N2SliderGeneratorPluginAbstract {

        protected $name = 'facebook';

        protected $needConfiguration = true;

        public function getLabel() {
            return 'Facebook';
        }

        protected function loadSources() {

            new N2GeneratorFacebookAlbums($this, 'albums', n2_('Photos by album'));
            new N2GeneratorFacebookPostsByPage($this, 'postsbypage', n2_('Posts by page'));
        }

        public function getPath() {
            return dirname(__FILE__) . DIRECTORY_SEPARATOR;
        }

        protected function initConfiguration() {
            static $loaded = false;
            if (!$loaded) {
                require_once dirname(__FILE__) . '/configuration.php';
                $this->configuration = new N2SliderGeneratorFacebookConfiguration($this);

                $loaded = true;
            }
        }
    }

    N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorFacebook);
}
