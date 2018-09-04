<?php
N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryIFrame extends N2SSPluginItemFactoryAbstract {

    protected $type = 'iframe';

    protected $priority = 100;

    protected $layerProperties = array(
        "desktopportraitwidth"  => 300,
        "desktopportraitheight" => 300
    );

    protected $class = 'N2SSItemIframe';

    public function __construct() {
        $this->title = n2_x('iframe', 'Slide item');
        $this->group = n2_('Advanced');
    }

    function getValues() {
        return array(
            'url'    => 'https://smartslider3.com/iframe/',
            'size'   => '100%|*|100%',
            'scroll' => 'yes'
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public static function getFilled($slide, $data) {
        $data->set('url', $slide->fill($data->get('url', '')));

        return $data;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-iframe');

        new N2ElementText($settings, 'url', n2_('iframe url'), '', array(
            'style' => 'width:280px;'
        ));

        new N2ElementList($settings, 'scroll', n2_('Scroll'), 'auto', array(
            'options' => array(
                'yes'  => n2_('Yes'),
                'no'   => n2_('No'),
                'auto' => n2_('Auto')
            )
        ));

        $size = new N2ElementMixed($settings, 'size', '', '100%|*|100%', array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementText($size, 'size-1', n2_('Width'), '', array(
            'style' => 'width:40px;'
        ));
        new N2ElementText($size, 'size-2', n2_('Height'), '', array(
            'style' => 'width:40px;'
        ));
    }
}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryIFrame);
