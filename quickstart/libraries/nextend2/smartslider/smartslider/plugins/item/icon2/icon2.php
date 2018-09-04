<?php
class N2SSPluginItemFactoryIcon2 extends N2SSPluginItemFactoryAbstract {

    public $type = 'icon2';

    protected $priority = 5;

    protected $layerProperties = array("desktopportraitwidth" => 50);

    protected $class = 'N2SSItemIcon2';

    public function __construct() {
        $this->title = n2_x('Icon', 'Slide item');
        $this->group = n2_('Image');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    function getValues() {
        return array(
            'icon'       => 'fa:smile-o',
            'color'      => '00000080',
            'colorhover' => '00000000',
            'size'       => 100,
            'link'       => '#|*|_self',
            'style'      => ''
        );
    }

    public static function getFilled($slide, $data) {
        $data->set('icon', $slide->fill($data->get('icon', '')));
        $data->set('link', $slide->fill($data->get('link', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addVisual($data->get('style'));
        $export->addLightbox($data->get('link'));
    }

    public function prepareImport($import, $data) {
        $data->set('style', $import->fixSection($data->get('style')));
        $data->set('link', $import->fixLightbox($data->get('link')));

        return $data;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-icon2');

        $icon = new N2ElementGroup($settings, 'item-icon2-icon');
        new N2ElementIcon2Manager($icon, 'icon', n2_('Icon'));
        new N2ElementColor($icon, 'color', n2_('Color'), '00000080', array(
            'alpha' => true
        ));
        new N2ElementColor($icon, 'colorhover', n2_('Hover color'), '00000000', array(
            'alpha' => true
        ));

        new N2ElementNumberSlider($settings, 'size', n2_('Size'), 24, array(
            'min'       => 4,
            'max'       => 10000,
            'sliderMax' => 200,
            'step'      => 4,
            'wide'      => 3,
            'unit'      => 'px'
        ));

        new N2ElementStyle($settings, 'style', n2_('Style') . ' - ' . n2_('Icon'), '', array(
            'previewMode' => 'box',
            'preview'     => '<div class="{styleClassName}" style="width:{nextend.activeLayer.find(\'.n2-ss-item\').width()}px;height:{nextend.activeLayer.find(\'.n2-ss-item\').height()}px;">{nextend.activeLayer.find(\'img\').clone().wrap(\'<p>\').parent().html()}</div>',
            'rowClass'    => 'n2-hidden'
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
        new N2ElementList($link, 'link-3', 'Rel', '', array(
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

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryIcon2);
