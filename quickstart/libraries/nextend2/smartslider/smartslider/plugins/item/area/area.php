<?php
N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryArea extends N2SSPluginItemFactoryAbstract {

    public $type = 'area';

    protected $priority = 100;

    protected $class = 'N2SSItemArea';

    protected $layerProperties = array(
        "desktopportraitwidth"  => 150,
        "desktopportraitheight" => 150
    );

    public function __construct() {
        $this->title = n2_x('Area', 'Slide item');
        $this->group = n2_('Advanced');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    function getValues() {
        return array(
            'width'        => '',
            'height'       => '',
            'color'        => '000000ff',
            'gradient'     => 'off',
            'color2'       => '000000ff',
            'css'          => '',
            'borderWidth'  => 0,
            'borderColor'  => 'ffffff1f',
            'borderRadius' => 0,
            'link'         => '#|*|_self'
        );
    }

    public function prepareExport($export, $data) {
        $export->addLightbox($data->get('link'));
    }

    public function prepareImport($import, $data) {
        $data->set('link', $import->fixLightbox($data->get('link')));

        return $data;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-area');

        $colors = new N2ElementGroup($settings, 'item-area-colors');
        new N2ElementColor($colors, 'color', n2_('Background color'), '00000000', array(
            'alpha' => true
        ));

        new N2ElementList($colors, 'gradient', n2_('Gradient'), 'off', array(
            'options' => array(
                'off'        => n2_('Off'),
                'vertical'   => '&darr;',
                'horizontal' => '&rarr;',
                'diagonal1'  => '&#8599;',
                'diagonal2'  => '&#8600;'
            )
        ));

        new N2ElementColor($colors, 'color2', n2_('Color end'), 'ffffff00', array(
            'alpha' => true
        ));

        $size = new N2ElementGroup($settings, 'item-area-size');
        new N2ElementNumber($size, 'width', n2_('Width'), '', array(
            'wide' => 4,
            'unit' => 'px'
        ));
        new N2ElementNumber($size, 'height', n2_('Height'), '', array(
            'wide' => 4,
            'unit' => 'px'
        ));

        new N2ElementTextarea($settings, 'css', n2_('Custom CSS'), '', array(
            'rowClass' => 'n2-expert'
        ));

        $border = new N2ElementGroup($settings, 'item-area-border');
        new N2ElementNumber($border, 'borderRadius', n2_('Border radius'), 0, array(
            'wide' => 3,
            'unit' => 'px'
        ));
        new N2ElementNumber($border, 'borderWidth', n2_('Border'), 0, array(
            'wide' => 3,
            'min'  => 0,
            'unit' => 'px'
        ));
        new N2ElementColor($border, 'borderColor', n2_('Color'), '00000000', array(
            'alpha' => true
        ));

        $link = new N2ElementMixed($settings, 'link', '', '|*|_self|*|');
        new N2ElementUrl($link, 'link-1', n2_('Link'), '', array(
            'style' => 'width:236px;'
        ));
        new N2ElementList($link, 'link-2', n2_('Target window'), '', array(
            'options' => array(
                '_self'  => n2_('Self'),
                '_blank' => n2_('New')
            )
        ));
        new N2ElementList($link, 'link-3', n2_('Rel'), '', array(
            'options' => array(
                ''           => '',
                'nofollow'   => 'nofollow',
                'noreferrer' => 'noreferrer',
                'author'     => 'author',
                'external'   => 'external',
                'help'       => 'help'
            )
        ));
    }


}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryArea);
