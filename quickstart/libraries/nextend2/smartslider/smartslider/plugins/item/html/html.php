<?php
N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryHTML extends N2SSPluginItemFactoryAbstract {

    protected $type = 'html';

    protected $priority = 102;

    protected $layerProperties = array("desktopportraitwidth" => 200);

    protected $class = 'N2SSItemHTML';

    public function __construct() {
        $this->title = n2_x('HTML', 'Slide item');
        $this->group = n2_('Advanced');
    }

    function getValues() {
        return array(
            'html'      => '<div>Empty element</div>',
            'css'       => '.selector{

}',
            'textalign' => 'inherit'
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public static function getFilled($slide, $data) {
        $data->set('html', $slide->fill($data->get('html', '')));

        return $data;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-html');

        new N2ElementTextAlign($settings, 'textalign', n2_('Text align'), 'inherit');
        new N2ElementTextarea($settings, 'html', 'HTML', '', array(
            'fieldStyle' => 'height: 200px; width: 230px;resize: vertical;'
        ));
        new N2ElementTextarea($settings, 'css', 'CSS', '', array(
            'fieldStyle' => 'height: 200px; width: 230px;resize: vertical;'
        ));
    }
}

N2SmartSliderItemsFactory::addItem(new  N2SSPluginItemFactoryHTML);
