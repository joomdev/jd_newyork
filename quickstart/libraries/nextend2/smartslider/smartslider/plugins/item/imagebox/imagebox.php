<?php
N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSPluginItemFactoryImageBox extends N2SSPluginItemFactoryAbstract {

    protected $type = 'imagebox';

    protected $priority = 6;

    protected $layerProperties = array();

    protected $class = 'N2SSItemImageBox';

    private $fontTitle = '{"name":"Static","data":[{"extra":"","color":"ffffffff","size":"32||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"1.5","bold":0,"italic":0,"underline":0,"align":"inherit","letterspacing":"normal","wordspacing":"normal","texttransform":"none"}]}';

    private $fontDescription = '{"name":"Static","data":[{"extra":"","color":"ffffffff","size":"16||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"2","bold":0,"italic":0,"underline":0,"align":"inherit","letterspacing":"normal","wordspacing":"normal","texttransform":"none"}]}';

    private $style = '';


    public function __construct() {
        $this->title = n2_x('Image box', 'Slide item');
        $this->group = n2_('Image');
    }

    public function loadResources($renderable) {
        parent::loadResources($renderable);

        $renderable->addLess($this->getPath() . "/imagebox.n2less", array(
            "sliderid" => $renderable->elementId
        ));
    }

    function getValues() {
        self::initDefault();

        return array(
            'layout'          => 'top',
            'padding'         => '10|*|10|*|10|*|10',
            'inneralign'      => 'center',
            'verticalalign'   => 'flex-start',
            'image'           => '$system$/images/placeholder/image.png',
            'imagewidth'      => 100,
            'imagealt'        => '',
            'icon'            => '',
            'iconsize'        => 64,
            'iconcolor'       => 'ffffffff',
            'heading'         => n2_('Heading'),
            'headingpriority' => 'div',
            'fonttitle'       => $this->fontTitle,
            'description'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
            'fontdescription' => $this->fontDescription,
            'style'           => $this->style,
            'link'            => '#|*|_self'
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public static function getFilled($slide, $data) {
        $data->set('heading', $slide->fill($data->get('heading', '')));
        $data->set('description', $slide->fill($data->get('description', '')));
        $data->set('link', $slide->fill($data->get('link', '#|*|')));
        $data->set('image', $slide->fill($data->get('image', '')));
        $data->set('imagealt', $slide->fill($data->get('imagealt', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addImage($data->get('image'));
        $export->addVisual($data->get('fonttitle'));
        $export->addVisual($data->get('fontdescription'));
        $export->addVisual($data->get('style'));
        $export->addLightbox($data->get('link'));
    }

    public function prepareImport($import, $data) {
        $data->set('image', $import->fixImage($data->get('image')));
        $data->set('fonttitle', $import->fixSection($data->get('fonttitle')));
        $data->set('fontdescription', $import->fixSection($data->get('fontdescription')));
        $data->set('style', $import->fixSection($data->get('style')));
        $data->set('link', $import->fixLightbox($data->get('link')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('image', N2ImageHelper::fixed($data->get('image')));

        return $data;
    }

    private function initDefault() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-imagebox-fonttitle');
            if (is_array($res)) {
                $this->fontTitle = $res['value'];
            }
            if (is_numeric($this->fontTitle)) {
                N2FontRenderer::preLoad($this->fontTitle);
            }

            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-imagebox-fontdescription');
            if (is_array($res)) {
                $this->fontDescription = $res['value'];
            }
            if (is_numeric($this->fontDescription)) {
                N2FontRenderer::preLoad($this->fontDescription);
            }

            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-imagebox-style');
            if (is_array($res)) {
                $this->style = $res['value'];
            }
            if (is_numeric($this->style)) {
                N2StyleRenderer::preLoad($this->style);
            }
            $inited = true;
        }
    }

    public function globalDefaultItemFontAndStyle($fontTab, $styleTab) {
        self::initDefault();

        new N2ElementFont($fontTab, 'item-imagebox-fonttitle', n2_('Item') . ' - ' . n2_('Image box') . ' ' . n2_('Title'), $this->fontTitle, array(
            'previewMode' => 'hover'
        ));

        new N2ElementFont($fontTab, 'item-imagebox-fontdescription', n2_('Item') . ' - ' . n2_('Image box') . ' ' . n2_('Description'), $this->fontDescription, array(
            'previewMode' => 'paragraph'
        ));

        new N2ElementStyle($styleTab, 'item-imagebox-style', n2_('Item') . ' - ' . n2_('Image box'), $this->style, array(
            'previewMode' => 'heading'
        ));
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-imagebox');

        $layout = new N2ElementGroup($settings, 'item-imagebox-layout');
        new N2ElementList($layout, 'layout', n2_('Layout'), '', array(
            'options' => array(
                'top'    => n2_('Top'),
                'left'   => n2_('Left'),
                'right'  => n2_('Right'),
                'bottom' => n2_('Bottom')
            )
        ));
        $padding = new N2ElementMarginPadding($layout, 'padding', n2_('Padding'), '10|*|10|*|10|*|10', array(
            'unit' => 'px'
        ));

        for ($i = 1; $i < 5; $i++) {
            new N2ElementNumberAutocomplete($padding, 'padding-' . $i, false, '', array(
                'values' => array(
                    0,
                    5,
                    10,
                    20,
                    30
                ),
                'wide'   => 3
            ));
        }

        $alignment = new N2ElementGroup($settings, 'item-imagebox-alignment');
        new N2ElementInnerAlign($alignment, 'inneralign', n2_('Inner align'));
        new N2ElementList($alignment, 'verticalalign', n2_('Vertical align'), '', array(
            'options' => array(
                'flex-start' => n2_('Top'),
                'center'     => n2_('Center'),
                'flex-end'   => n2_('Bottom')
            )
        ));


        $image = new N2ElementGroup($settings, 'item-imagebox-image');

        new N2ElementImage($image, 'image', n2_('Image'), '', array(
            'fixed'      => true,
            'style'      => 'width:236px;',
            'relatedAlt' => 'item_imageboxalt'
        ));

        new N2ElementNumberSlider($image, 'imagewidth', n2_('Width'), '', array(
            'max'  => 100,
            'unit' => '%',
            'wide' => 3
        ));
        new N2ElementText($image, 'alt', 'SEO - ' . n2_('Alt tag'), '', array(
            'style' => 'width:215px;'
        ));


        $icon = new N2ElementGroup($settings, 'item-imagebox-icon');
        new N2ElementIcon2Manager($icon, 'icon', n2_('Icon'), '', array(
            'hasClear' => true
        ));
        new N2ElementNumberSlider($icon, 'iconsize', n2_('Size'), 100, array(
            'min'       => 8,
            'max'       => 400,
            'sliderMax' => 200,
            'step'      => 4,
            'wide'      => 4,
            'unit'      => 'px'
        ));
        new N2ElementColor($icon, 'iconcolor', n2_('Color'), '', array(
            'alpha' => true
        ));

        $heading = new N2ElementGroup($settings, 'item-imagebox-heading');
        new N2ElementTextarea($heading, 'heading', n2_('Heading'));
        new N2ElementList($heading, 'headingpriority', n2_('Tag'), 'div', array(
            'options'  => array(
                'div' => 'div',
                '1'   => 'H1',
                '2'   => 'H2',
                '3'   => 'H3',
                '4'   => 'H4',
                '5'   => 'H5',
                '6'   => 'H6'
            ),
            'rowClass' => 'n2-expert'
        ));

        new N2ElementFont($settings, 'fonttitle', n2_('Font') . ' - ' . n2_('Heading'), '', array(
            'previewMode' => 'hover',
            'preview'     => '<div class="{fontClassName}">{$(\'#item_imageboxheading\').val().replace(/\\n/g, \'<br />\');}</div>',
            'set'         => 1000,
            'rowClass'    => 'n2-hidden'
        ));

        new N2ElementRichTextarea($settings, 'description', n2_('Description'), '', array(
            'fieldStyle' => 'height: 120px;'
        ));

        new N2ElementFont($settings, 'fontdescription', n2_('Font') . ' - ' . n2_('Description'), '', array(
            'previewMode' => 'paragraph',
            'preview'     => '<div style="width:{nextend.activeLayer.width()}px;"><p class="{fontClassName}">{$(\'#item_textcontent\').val();}</p></div>',
            'set'         => 1000,
            'rowClass'    => 'n2-hidden'
        ));

        new N2ElementStyle($settings, 'style', n2_('Style') . ' - ' . n2_('Image box'), '', array(
            'previewMode' => 'box',
            'preview'     => '<div class="{styleClassName}" style="width:{nextend.activeLayer.width()}px;height:{nextend.activeLayer.height()}px;"></div>',
            'set'         => 1000,
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

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryImageBox);
