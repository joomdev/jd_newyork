<?php
N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryInput extends N2SSPluginItemFactoryAbstract {

    protected $type = 'input';

    protected $priority = 100;

    private $inputFont = 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiIwMDAwMDBmZiIsInNpemUiOiIxNXx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJhZm9udCI6Ik1vbnRzZXJyYXQsQXJpYWwiLCJsaW5laGVpZ2h0IjoiNDRweCIsImJvbGQiOjAsIml0YWxpYyI6MCwidW5kZXJsaW5lIjowLCJhbGlnbiI6ImxlZnQiLCJsZXR0ZXJzcGFjaW5nIjoibm9ybWFsIiwid29yZHNwYWNpbmciOiJub3JtYWwiLCJ0ZXh0dHJhbnNmb3JtIjoibm9uZSIsImV4dHJhIjoiaGVpZ2h0OjQ0cHg7In0se30se31dfQ==';

    private $buttonFont = 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiJmZmZmZmZmZiIsInNpemUiOiIxNHx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJhZm9udCI6Ik1vbnRzZXJyYXQsQXJpYWwiLCJsaW5laGVpZ2h0IjoiNDRweCIsImJvbGQiOjAsIml0YWxpYyI6MCwidW5kZXJsaW5lIjowLCJhbGlnbiI6ImxlZnQiLCJsZXR0ZXJzcGFjaW5nIjoibm9ybWFsIiwid29yZHNwYWNpbmciOiJub3JtYWwiLCJ0ZXh0dHJhbnNmb3JtIjoibm9uZSIsImV4dHJhIjoiIn0se30se31dfQ==';

    private $style = '';
    private $inputStyle = 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiZmZmZmZmZmYiLCJwYWRkaW5nIjoiMHwqfDIwfCp8MHwqfDIwfCp8cHgiLCJib3hzaGFkb3ciOiIwfCp8MHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJib3JkZXIiOiIwfCp8c29saWR8KnwwMDAwMDBmZiIsImJvcmRlcnJhZGl1cyI6IjAiLCJleHRyYSI6IiJ9LHt9XX0=';
    private $buttonStyle = 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDRiYzhmZmYiLCJwYWRkaW5nIjoiMHwqfDM1fCp8MHwqfDM1fCp8cHgiLCJib3hzaGFkb3ciOiIwfCp8MHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJib3JkZXIiOiIwfCp8c29saWR8KnwwMDAwMDBmZiIsImJvcmRlcnJhZGl1cyI6IjAiLCJleHRyYSI6IiJ9LHt9XX0=';

    protected $class = 'N2SSItemInput';

    public function __construct() {
        $this->title = n2_x('Input', 'Slide item');
        $this->group = n2_('Advanced');
    }

    private function initDefaultFont() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-input-font');
            if (is_array($res)) {
                $this->inputFont = $res['value'];
            }
            if (is_numeric($this->inputFont)) {
                N2FontRenderer::preLoad($this->inputFont);
            }
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-input-button-font');
            if (is_array($res)) {
                $this->buttonFont = $res['value'];
            }
            if (is_numeric($this->buttonFont)) {
                N2FontRenderer::preLoad($this->buttonFont);
            }
            $inited = true;
        }
    }

    private function initDefaultStyle() {
        static $inited = false;
        if (!$inited) {
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-input-container-style');
            if (is_array($res)) {
                $this->style = $res['value'];
            }
            if (is_numeric($this->style)) {
                N2StyleRenderer::preLoad($this->style);
            }
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-input-style');
            if (is_array($res)) {
                $this->inputStyle = $res['value'];
            }
            if (is_numeric($this->inputStyle)) {
                N2StyleRenderer::preLoad($this->inputStyle);
            }
            $res = N2StorageSectionAdmin::get('smartslider', 'default', 'item-input-button-style');
            if (is_array($res)) {
                $this->buttonStyle = $res['value'];
            }
            if (is_numeric($this->buttonStyle)) {
                N2StyleRenderer::preLoad($this->buttonStyle);
            }
            $inited = true;
        }
    }

    public function globalDefaultItemFontAndStyle($fontTab, $styleTab) {
        self::initDefaultFont();
        new N2ElementFont($fontTab, 'item-input-font', n2_('Item') . ' - ' . n2_('Input'), $this->inputFont, array(
            'previewMode' => 'paragraph'
        ));

        new N2ElementFont($fontTab, 'item-input-button-font', n2_('Item') . ' - ' . n2_('Input button'), $this->buttonFont, array(
            'previewMode' => 'hover'
        ));

        self::initDefaultStyle();

        new N2ElementStyle($styleTab, 'item-input-container-style', n2_('Item') . ' - ' . n2_('Input container'), $this->style, array(
            'previewMode' => 'heading'
        ));

        new N2ElementStyle($styleTab, 'item-input-style', n2_('Item') . ' - ' . n2_('Input'), $this->inputStyle, array(
            'previewMode' => 'heading'
        ));

        new N2ElementStyle($styleTab, 'item-input-button-style', n2_('Item') . ' - ' . n2_('Input button'), $this->buttonStyle, array(
            'previewMode' => 'button'
        ));
    }


    function getValues() {
        self::initDefaultFont();
        self::initDefaultStyle();

        return array(
            'placeholder' => n2_('What are you looking for?'),
            'action'      => 'https://www.google.com/search',
            'method'      => 'GET',
            'target'      => '_self',
            'parameters'  => 'ie=utf-8&oe=utf-8',
            'name'        => 'q',
            'inputfont'   => $this->inputFont,
            'buttonfont'  => $this->buttonFont,
            'style'       => $this->style,
            'inputstyle'  => $this->inputStyle,
            'buttonstyle' => $this->buttonStyle,
            'buttonlabel' => n2_('Search'),
            'submit'      => '',
            'class'       => '',
            'onsubmit'    => '',
            'onkeyup'     => ''
        );
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public static function getFilled($slide, $data) {
        $data->set('parameters', $slide->fill($data->get('parameters')));
        $data->set('buttonlabel', $slide->fill($data->get('buttonlabel')));
        $data->set('action', $slide->fill($data->get('action')));
        $data->set('name', $slide->fill($data->get('name')));
        $data->set('placeholder', $slide->fill($data->get('placeholder')));

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

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-input');

        new N2ElementStyle($settings, 'style', n2_('Style') . ' - ' . n2_('Container'), '', array(
            'previewMode' => 'heading',
            'preview'     => '<div style="width:{nextend.activeLayer.width()}px;height:{nextend.activeLayer.height()}px;" class="{styleClassName}"></div>',
            'set'         => 1000,
            'rowClass'    => 'n2-hidden'
        ));

        $input = new N2ElementGroup($settings, 'item-input-input');
        new N2ElementText($input, 'name', n2_('Input name'), 'q', array(
            'style' => 'width:80px;'
        ));
        new N2ElementText($input, 'placeholder', n2_('Placeholder text'), n2_('What are you looking for?'), array(
            'style' => 'width:170px;'
        ));

        new N2ElementFont($input, 'inputfont', n2_('Font') . ' - ' . n2_('Input'), '', array(
            'previewMode' => 'paragraph',
            'preview'     => '<div style="width:{nextend.activeLayer.prop(\'style\').width};display:inline-block;"><div class="{styleClassName} {fontClassName}">{$(\'#item_inputplaceholder\').val();}</div></div>',
            'set'         => 1000,
            'style'       => 'item_inputinputstyle',
            'rowClass'    => 'n2-hidden'
        ));

        new N2ElementStyle($settings, 'inputstyle', n2_('Style') . ' - ' . n2_('Input'), '', array(
            'previewMode' => 'heading',
            'preview'     => '<div style="width:{nextend.activeLayer.prop(\'style\').width};display:inline-block;"><div class="{styleClassName} {fontClassName}">{$(\'#item_inputplaceholder\').val();}</div></div>',
            'set'         => 1000,
            'font'        => 'item_inputinputfont',
            'rowClass'    => 'n2-hidden'
        ));

        new N2ElementText($settings, 'buttonlabel', n2_('Label'), n2_('Button'), array(
            'style' => 'width:280px;'
        ));

        new N2ElementFont($settings, 'buttonfont', n2_('Font') . ' - ' . n2_('Button'), '', array(
            'previewMode' => 'hover',
            'preview'     => '<div class="{fontClassName} {styleClassName}" style="width:{nextend.activeLayer.prop(\'style\').width};">{$(\'#item_inputbuttonlabel\').val();}</div>',
            'set'         => 1000,
            'style'       => 'item_inputbuttonstyle',
            'rowClass'    => 'n2-hidden'
        ));

        new N2ElementStyle($settings, 'buttonstyle', n2_('Style') . ' - ' . n2_('Button'), '', array(
            'previewMode' => 'heading',
            'preview'     => '<div class="{fontClassName} {styleClassName}" style="width:{nextend.activeLayer.prop(\'style\').width};">{$(\'#item_inputbuttonlabel\').val();}</div>',
            'set'         => 1000,
            'font'        => 'item_inputbuttonfont',
            'rowClass'    => 'n2-hidden'
        ));

        $form = new N2ElementGroup($settings, 'item-input-form');
        new N2ElementText($form, 'action', n2_('Form action'), 'https://www.google.com/search', array(
            'style' => 'width:280px;'
        ));
        new N2ElementList($form, 'method', n2_('Method'), 'GET', array(
            'options' => array(
                'GET'  => 'GET',
                'POST' => 'POST'
            )
        ));
        new N2ElementList($form, 'target', n2_('Target window'), '_self', array(
            'options' => array(
                '_self'  => n2_('Self'),
                '_blank' => n2_('New')
            )
        ));
        new N2ElementList($form, 'submit', n2_('Slide action to submit'), '', array(
            'rowClass' => 'n2-expert',
            'options'  => array(
                ''           => n2_('Off'),
                'click'      => n2_('Click'),
                'mouseenter' => n2_('Mouse enter'),
                'mouseleave' => n2_('Mouse leave')
            )
        ));
        new N2ElementTextarea($form, 'parameters', n2_('Parameters'), 'ie=utf-8&oe=utf-8', array(
            'fieldStyle' => 'width: 280px;resize: vertical;'
        ));

        $js = new N2ElementGroup($settings, 'item-input-js', 'JavaScript', array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementText($js, 'onsubmit', 'OnSubmit', '', array(
            'style' => 'width:130px;'
        ));

        new N2ElementText($js, 'onkeyup', 'OnKeyUp', '', array(
            'style' => 'width:130px;'
        ));

        new N2ElementText($settings, 'class', 'CSS class', '', array(
            'style'    => 'width:280px;',
            'rowClass' => 'n2-expert'
        ));
    }

}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryInput);

