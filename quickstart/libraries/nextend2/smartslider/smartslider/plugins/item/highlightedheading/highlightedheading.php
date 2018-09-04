<?php

N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryHighlightedHeading extends N2SSPluginItemFactoryAbstract {

    protected $type = 'highlightedHeading';

    protected $priority = 100;

    private $font = '{"name":"Static","data":[{"extra":"","color":"ffffffff","size":"36||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"1.5","bold":0,"italic":0,"underline":0,"align":"inherit","letterspacing":"normal","wordspacing":"normal","texttransform":"none"},{},{}]}';

    private $style = '{"name":"Static","data":[{},{"padding":"0|*|0|*|0|*|0|*|px"},{"padding":"0|*|0|*|0|*|0|*|px"}]}';

    protected $class = 'N2SSItemHighlightedHeading';

    public function __construct() {
        $this->title = n2_x('Highlighted Heading', 'Slide item');
        $this->group = n2_('Content');
    }

    function getValues() {
        self::initDefault();

        return array(
            'type'  => 'circle1',
            'color' => '5CBA3CFF',
            'width' => 10,
            'front' => 0,

            'before-text'      => 'This page is',
            'highlighted-text' => 'Amazing',
            'after-text'       => '',

            'animate'    => 1,
            'delay'      => 0,
            'duration'   => 1500,
            'loop'       => 0,
            'loop-delay' => 2000,

            'link' => '#|*|_self',

            'priority' => 'div',

            'font'  => $this->font,
            'style' => $this->style,

            'class' => ''
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public function loadResources($renderable) {
        parent::loadResources($renderable);

        $renderable->addLess($this->getPath() . "/highlightedHeading.n2less", array(
            "sliderid" => $renderable->elementId
        ));

        $renderable->addScript('N2Classes.ItemHighlightedHeading.svg=' . json_encode($this->getTypes()) . ';');
    }

    private function getTypes() {
        static $types = null;
        if ($types === null) {
            $types     = array();
            $extension = 'svg';
            $folder    = $this->getPath() . 'svg/';
            $files     = N2Filesystem::files($folder);
            for ($i = 0; $i < count($files); $i++) {
                $pathInfo = pathinfo($files[$i]);
                if (isset($pathInfo['extension']) && $pathInfo['extension'] == $extension) {
                    $types[$pathInfo['filename']] = file_get_contents($folder . $files[$i]);
                }
            }
        }

        return $types;
    }

    private function getTypeOptions() {
        return array(
            ''                  => n2_('None'),
            'circle1'           => n2_('Circle 1'),
            'circle2'           => n2_('Circle 2'),
            'circle3'           => n2_('Circle 3'),
            'curly1'            => n2_('Curly 1'),
            'curly2'            => n2_('Curly 2'),
            'highlight1'        => n2_('Highlight 1'),
            'highlight2'        => n2_('Highlight 2'),
            'highlight3'        => n2_('Highlight 3'),
            'line_through1'     => n2_('Line Through 1'),
            'line_through2'     => n2_('Line Through 2'),
            'line_through3'     => n2_('Line Through 3'),
            'rectangle1'        => n2_('Rectangle 1'),
            'rectangle2'        => n2_('Rectangle 2'),
            'underline1'        => n2_('Underline 1'),
            'underline2'        => n2_('Underline 2'),
            'underline3'        => n2_('Underline 3'),
            'underline_double1' => n2_('Underline double 1'),
            'underline_double2' => n2_('Underline double 2'),
            'zigzag1'           => n2_('ZigZag 1'),
            'zigzag2'           => n2_('ZigZag 2'),
            'zigzag3'           => n2_('ZigZag 3'),
        );
    }

    public static function getFilled($slide, $data) {
        $data->set('heading', $slide->fill($data->get('heading', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('style'));
    }

    public function prepareImport($import, $data) {
        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('style', $import->fixSection($data->get('style')));

        return $data;
    }

    private function initDefault() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-heading-font');
            if (is_array($res)) {
                $this->font = $res['value'];
            }
            if (is_numeric($this->font)) {
                N2FontRenderer::preLoad($this->font);
            }

            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-heading-style');
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

        new N2ElementFont($fontTab, 'item-heading-font', n2_('Item') . ' - ' . n2_('Heading'), $this->font, array(
            'previewMode' => 'hover'
        ));

        new N2ElementStyle($styleTab, 'item-heading-style', n2_('Item') . ' - ' . n2_('Heading'), $this->style, array(
            'previewMode' => 'heading'
        ));
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-highlighted-heading');

        $highlight = new N2ElementGroup($settings, 'item-highlighted-heading-highlight');

        new N2ElementList($highlight, 'type', n2_('Type'), '', array(
            'options'       => $this->getTypeOptions(),
            'relatedFields' => array(
                'color',
                'width',
                'front'
            )
        ));
        new N2ElementColor($highlight, 'color', n2_('Color'), '', array(
            'alpha' => true
        ));
        new N2ElementNumberSlider($highlight, 'width', n2_('Width'), '', array(
            'max'  => 100,
            'min'  => 1,
            'unit' => 'px',
            'wide' => 3
        ));
        new N2ElementOnOff($highlight, 'front', n2_('Bring front'), 0);

        new N2ElementText($settings, 'before-text', n2_('Before text'), '', array(
            'style' => 'width: 230px;'
        ));

        new N2ElementText($settings, 'highlighted-text', n2_('Highlighted text'), '', array(
            'style' => 'width: 230px;'
        ));

        new N2ElementText($settings, 'after-text', n2_('After text'), '', array(
            'style' => 'width: 230px;'
        ));


        $animation = new N2ElementGroup($settings, 'item-highlightheading-animation');
        new N2ElementOnOff($animation, 'animate', n2_('Animate'), 1, array(
            'relatedFields' => array(
                'delay',
                'duration',
                'loop',
                'loop-delay'
            )
        ));
        new N2ElementNumber($animation, 'delay', n2_('Delay'), 0, array(
            'unit' => 'ms',
            'wide' => 5
        ));
        new N2ElementNumber($animation, 'duration', n2_('Duration'), 1500, array(
            'unit' => 'ms',
            'wide' => 5,
            'post' => 'break'
        ));

        new N2ElementOnOff($animation, 'loop', n2_('Loop'), 1);
        new N2ElementNumber($animation, 'loop-delay', n2_('Loop delay'), 0, array(
            'unit' => 'ms',
            'wide' => 5
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

        $other = new N2ElementGroup($settings, 'item-highlightheading-other');
        new N2ElementList($other, 'priority', 'Tag', 'div', array(
            'options' => array(
                'div' => 'div',
                '1'   => 'H1',
                '2'   => 'H2',
                '3'   => 'H3',
                '4'   => 'H4',
                '5'   => 'H5',
                '6'   => 'H6'
            )
        ));

        new N2ElementFont($settings, 'font', n2_('Font') . ' - ' . n2_('Heading'), '', array(
            'previewMode' => 'highlight',
            'preview'     => '<div style="width:{nextend.activeLayer.prop(\'style\').width};"><div class="{styleClassName} {fontClassName}">Lorem ipsum dolor sit amet, <div class="n2-highlighted" style="display:inline-block;">consectetur</div> adipiscing elit</div></div>',
            'set'         => 1000,
            'style'       => 'item_highlightedHeadingstyle',
            'rowClass'    => 'n2-hidden'
        ));

        new N2ElementStyle($settings, 'style', n2_('Style') . ' - ' . n2_('Heading'), '', array(
            'previewMode' => 'highlight',
            'preview'     => '<div style="width:{nextend.activeLayer.prop(\'style\').width};"><div class="{styleClassName} {fontClassName}">Lorem ipsum dolor sit amet, <div class="n2-highlighted" style="display:inline-block;">consectetur</div> adipiscing elit</div></div>',
            'set'         => 1000,
            'font'        => 'item_highlightedHeadingfont',
            'rowClass'    => 'n2-hidden'
        ));


        new N2ElementText($settings, 'class', n2_('Custom CSS classes'), '', array(
            'style'    => 'width:174px;',
            'rowClass' => 'n2-expert'
        ));

    }

}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryHighlightedHeading);
