<?php
N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSPluginItemFactoryCounter extends N2SSPluginItemFactoryAbstract {

    protected $type = 'counter';

    protected $priority = 11;

    protected $layerProperties = array();

    protected $class = 'N2SSItemCounter';

    private $font = '{"name":"Static","data":[{"extra":"","color":"ffffffff","size":"40||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"1","bold":0,"italic":0,"underline":0,"align":"center","letterspacing":"normal","wordspacing":"normal","texttransform":"none"}]}';

    private $fontLabel = '{"name":"Static","data":[{"extra":"","color":"ffffffff","size":"16||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"2","bold":0,"italic":0,"underline":0,"align":"center","letterspacing":"normal","wordspacing":"normal","texttransform":"none"}]}';


    public function __construct() {
        $this->title = n2_x('Counter', 'Slide item');
        $this->group = n2_('Special');
    }

    function getValues() {
        self::initDefault();

        return array(
            'value'             => 50,
            'startvalue'        => 0,
            'pre'               => '',
            'post'              => '%',
            'label'             => '',
            'font'              => $this->font,
            'fontlabel'         => $this->fontLabel,
            'labelplacement'    => 'after',
            'animationduration' => 1000,
            'animationdelay'    => 0
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public static function getFilled($slide, $data) {
        $data->set('label', $slide->fill($data->get('label', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('fontlabel'));
    }

    public function prepareImport($import, $data) {
        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('fontlabel', $import->fixSection($data->get('fontlabel')));

        return $data;
    }

    private function initDefault() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-counter-font');
            if (is_array($res)) {
                $this->font = $res['value'];
            }
            if (is_numeric($this->font)) {
                N2FontRenderer::preLoad($this->font);
            }

            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-counter-fontlabel');
            if (is_array($res)) {
                $this->fontLabel = $res['value'];
            }
            if (is_numeric($this->fontLabel)) {
                N2FontRenderer::preLoad($this->fontLabel);
            }
            $inited = true;
        }
    }

    public function globalDefaultItemFontAndStyle($fontTab, $styleTab) {
        self::initDefault();
        new N2ElementFont($fontTab, 'item-counter-font', n2_('Item') . ' - ' . n2_('Counter'), $this->font, array(
            'previewMode' => 'simple'
        ));

        new N2ElementFont($fontTab, 'item-counter-fontlabel', n2_('Item') . ' - ' . n2_('Counter') . ' ' . n2_('label'), $this->fontLabel, array(
            'previewMode' => 'simple'
        ));
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-counter');

        $values = new N2ElementGroup($settings, 'item-counter-values');
        new N2ElementNumber($values, 'value', n2_('Value'), '', array(
            'wide' => 5
        ));
        new N2ElementNumber($values, 'startvalue', n2_('Start from'), '', array(
            'wide' => 5
        ));

        $prepost = new N2ElementGroup($settings, 'item-counter-prepost');
        new N2ElementText($prepost, 'pre', n2_('Label') . ' - ' . n2_('Pre'), '', array(
            'style' => '40px'
        ));
        new N2ElementText($prepost, 'post', n2_('Label') . ' - ' . n2_('Post'), '', array(
            'style' => '40px'
        ));

        $label = new N2ElementGroup($settings, 'item-counter-label');
        new N2ElementText($label, 'label', n2_('Label'), '', array(
            'style' => 'width:150px;'
        ));
        new N2ElementList($label, 'labelplacement', n2_('Placement'), '', array(
            'options' => array(
                'before' => n2_('Before'),
                'after'  => n2_('After')
            )
        ));

        $animation = new N2ElementGroup($settings, 'item-counter-animation');
        new N2ElementNumber($animation, 'animationduration', n2_('Animation duration'), 1, array(
            'min'  => 0,
            'wide' => 5,
            'unit' => 'ms'
        ));
        new N2ElementNumber($animation, 'animationdelay', n2_('Delay'), 0, array(
            'min'  => 0,
            'wide' => 5,
            'unit' => 'ms'
        ));


        new N2ElementFont($settings, 'font', n2_('Font') . ' - ' . n2_('Counter'), '', array(
            'previewMode' => 'simple',
            'preview'     => '<div class="{fontClassName}">100%</div>',
            'set'         => 1000,
            'rowClass'    => 'n2-hidden'
        ));

        new N2ElementFont($settings, 'fontlabel', n2_('Font') . ' - ' . n2_('Label'), '', array(
            'previewMode' => 'simple',
            'preview'     => '<div class="{fontClassName}">{$(\'#item_counterlabel\').val();}</div>',
            'set'         => 1000,
            'rowClass'    => 'n2-hidden'
        ));
    }

}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryCounter);
