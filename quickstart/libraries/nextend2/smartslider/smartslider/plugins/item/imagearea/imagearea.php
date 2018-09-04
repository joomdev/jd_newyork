<?php
N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryImageArea extends N2SSPluginItemFactoryAbstract {

    public $type = 'imagearea';

    protected $priority = 6;

    protected $layerProperties = array(
        "desktopportraitwidth"  => 150,
        "desktopportraitheight" => 150
    );

    protected $class = 'N2SSItemImageArea';

    public function __construct() {
        $this->title = n2_x('Image area', 'Slide item');
        $this->group = n2_('Image');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    function getValues() {
        return array(
            'image'     => '$system$/images/placeholder/image.png',
            'link'      => '#|*|_self',
            'fillmode'  => 'cover',
            'positionx' => 50,
            'positiony' => 50
        );
    }

    public function prepareExport($export, $data) {
        $export->addImage($data->get('image'));
        $export->addLightbox($data->get('link'));
    }

    public function prepareImport($import, $data) {
        $data->set('image', $import->fixImage($data->get('image')));
        $data->set('link', $import->fixLightbox($data->get('link')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('image', N2ImageHelper::fixed($data->get('image')));

        return $data;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-imagearea');

        new N2ElementImage($settings, 'image', n2_('Image'), '', array(
            'fixed'      => true,
            'style'      => 'width:236px;',
            'relatedAlt' => 'item_imagealt'
        ));

        $background = new N2ElementGroup($settings, 'item-imagearea-background');
        new N2ElementList($background, 'fillmode', n2_('Fill mode'), 'cover', array(
            'options' => array(
                'cover'   => n2_('Fill'),
                'contain' => n2_('Fit')
            )
        ));

        $backgroundPosition = new N2ElementGroup($background, 'item-imagearea-background-position', n2_('Background position'));
        new N2ElementNumber($backgroundPosition, 'positionx', '', 50, array(
            'min'      => 0,
            'max'      => 100,
            'wide'     => 3,
            'sublabel' => 'X',
            'unit'     => '%'
        ));
        new N2ElementNumber($backgroundPosition, 'positiony', '', 50, array(
            'min'      => 0,
            'max'      => 100,
            'wide'     => 3,
            'sublabel' => 'Y',
            'unit'     => '%'
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

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryImageArea);
