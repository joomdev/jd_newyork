<?php
N2Loader::import('libraries.slider.slides.slide.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryJoomlaModule extends N2SSPluginItemFactoryAbstract {

    protected $type = 'joomlamodule';

    protected $priority = 101;

    protected $group = 'Advanced';

    protected $class = 'N2SSItemJoomlaModule';

    public function __construct() {
        $this->title = 'Joomla Module';
        $this->group = n2_('Advanced');
    }

    function getValues() {
        return array(
            'positiontype'  => 'loadposition',
            'positionvalue' => ''
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-joomlamodule');

        new N2ElementList($settings, 'positiontype', n2_('Type'), 'loadposition', array(
            'options' => array(
                'loadposition' => 'Loadposition - Content plugin',
                'module'       => 'Module - Modules Anywhere',
                'modulepos'    => 'Modulepos - Modules Anywhere'
            )
        ));

        new N2ElementText($settings, 'positionvalue', n2_('Position name or module ID'));
    }
}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryJoomlaModule);

