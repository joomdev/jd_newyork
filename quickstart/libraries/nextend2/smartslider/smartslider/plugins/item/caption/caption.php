<?php
N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSPluginItemFactoryCaption extends N2SSPluginItemFactoryAbstract {

    protected $type = 'caption';

    protected $priority = 7;

    protected $layerProperties = array(
        "desktopportraitleft"  => 0,
        "desktopportraittop"   => 0,
        "desktopportraitwidth" => 200
    );

    private $fontTitle = 1003;
    private $font = 1003;

    protected $class = 'N2SSItemCaption';

    public function __construct() {
        $this->title = n2_x('Caption', 'Slide item');
        $this->group = n2_('Image');
    }

    private function initDefaultFont() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-caption-font-title');
            if (is_array($res)) {
                $this->fontTitle = $res['value'];
            }
            if (is_numeric($this->fontTitle)) {
                N2FontRenderer::preLoad($this->fontTitle);
            }
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-caption-font');
            if (is_array($res)) {
                $this->font = $res['value'];
            }
            if (is_numeric($this->font)) {
                N2FontRenderer::preLoad($this->font);
            }
            $inited = true;
        }
    }

    public function globalDefaultItemFontAndStyle($fontTab, $styleTab) {
        self::initDefaultFont();

        new N2ElementFont($fontTab, 'item-caption-font-title', n2_('Item') . ' - ' . n2_('Caption') . ' - ' . n2_('Title'), $this->fontTitle, array(
            'previewMode' => 'paragraph'
        ));

        new N2ElementFont($fontTab, 'item-caption-font', n2_('Item') . ' - ' . n2_('Caption') . ' - ' . n2_('Description'), $this->font, array(
            'previewMode' => 'paragraph'
        ));
    }

    public function loadResources($renderable) {
        parent::loadResources($renderable);

        $renderable->addLess($this->getPath() . "/caption.n2less", array(
            "sliderid" => $renderable->elementId
        ));
    }

    function getValues() {
        self::initDefaultFont();

        return array(
            'animation'      => 'Simple|*|left|*|0',
            'image'          => '$system$/images/placeholder/image.png',
            'alt'            => '',
            'link'           => '#|*|_self',
            'verticalalign'  => 'center',
            'content'        => n2_('Caption'),
            'description'    => '',
            'fonttitle'      => $this->fontTitle,
            'font'           => $this->font,
            'color'          => '00000080',
            'image-optimize' => 1
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public static function getFilled($slide, $data) {
        $data->set('image', $slide->fill($data->get('image', '')));
        $data->set('alt', $slide->fill($data->get('alt', '')));
        $data->set('content', $slide->fill($data->get('content', '')));
        $data->set('description', $slide->fill($data->get('description', '')));
        $data->set('link', $slide->fill($data->get('link', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addImage($data->get('image'));
        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('fonttitle'));
        $export->addLightbox($data->get('link'));
    }

    public function prepareImport($import, $data) {
        $data->set('image', $import->fixImage($data->get('image')));
        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('fonttitle', $import->fixSection($data->get('fonttitle')));
        $data->set('link', $import->fixLightbox($data->get('link')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('image', N2ImageHelper::fixed($data->get('image')));

        return $data;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-caption');

        $image = new N2ElementGroup($settings, 'item-caption-image');
        new N2ElementImage($image, 'image', n2_('Image'), '', array(
            'fixed'      => true,
            'relatedAlt' => 'item_captionalt',
            'style'      => 'width:236px;'
        ));
        new N2ElementText($image, 'alt', 'SEO - ' . n2_('Alt tag'), '', array(
            'rowClass' => 'n2-expert',
            'style'    => 'width:280px;'
        ));

        $animation = new N2ElementMixed($settings, 'animation', '', 'Full|*|Left|*|0');
        new N2ElementList($animation, 'animation-1', n2_('Animation'), '', array(
            'options' => array(
                'Full'   => n2_('Full'),
                'Simple' => n2_('Simple'),
                'Fade'   => n2_('Fade')
            )
        ));
        new N2ElementList($animation, 'animation-2', n2_('Direction'), '', array(
            'options' => array(
                'top'    => n2_('Top'),
                'right'  => n2_('Right'),
                'bottom' => n2_('Bottom'),
                'left'   => n2_('Left')
            )
        ));
        new N2ElementOnOff($animation, 'animation-3', n2_('Scale'));

        $overlay = new N2ElementGroup($settings, 'item-caption-overlay');
        new N2ElementColor($overlay, 'color', n2_('Overlay background'), '00000080', array(
            'alpha' => true
        ));
        new N2ElementList($overlay, 'verticalalign', n2_('Vertical align'), '', array(
            'options' => array(
                'flex-start' => n2_('Top'),
                'center'     => n2_('Center'),
                'flex-end'   => n2_('Bottom')
            )
        ));

        new N2ElementText($settings, 'content', n2_('Title'), '', array(
            'style' => 'width:280px;'
        ));
        new N2ElementFont($settings, 'fonttitle', n2_('Font') . ' - ' . n2_('Title'), '', array(
            'previewMode' => 'paragraph',
            'rowClass'    => 'n2-hidden'
        ));

        new N2ElementTextarea($settings, 'description', n2_('Description'), '', array());
        new N2ElementFont($settings, 'font', n2_('Font') . ' - ' . n2_('Description'), '', array(
            'previewMode' => 'paragraph',
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

        new N2ElementOnOff($settings, 'image-optimize', n2_('Optimize image'), 1, array(
            'rowClass' => 'n2-expert'
        ));

    }

}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryCaption);
