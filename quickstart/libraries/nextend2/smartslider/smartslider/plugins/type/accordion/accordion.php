<?php
class N2SSPluginTypeAccordion extends N2SSPluginSliderType {

    protected $name = 'accordion';

    public $ordering = 5;

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
    }

    public function getLabel() {
        return n2_x('Accordion', 'Slider type');
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'accordion-type', n2_('Accordion slider type') . ' - ' . n2_('Settings'));

        new N2ElementSkin($settings, 'slider-preset', n2_('Preset'), '', array(
            'fixed'   => true,
            'options' => array(
                'dark'  => array(
                    'label'    => n2_('Dark'),
                    'settings' => array(
                        'tab-normal-color'   => '3E3E3E',
                        'outer-border'       => 6,
                        'outer-border-color' => '3E3E3Eff',
                        'inner-border'       => 6,
                        'inner-border-color' => '222222ff',
                        'title-font'         => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siZXh0cmEiOiJ0ZXh0LXRyYW5zZm9ybTogdXBwZXJjYXNlOyIsImNvbG9yIjoiZmZmZmZmZmYiLCJzaXplIjoiMTR8fHB4IiwidHNoYWRvdyI6IjB8KnwwfCp8MHwqfDAwMDAwMGZmIiwiYWZvbnQiOiJNb250c2VycmF0IiwibGluZWhlaWdodCI6IjEuMyIsImJvbGQiOjAsIml0YWxpYyI6MCwidW5kZXJsaW5lIjowLCJhbGlnbiI6ImxlZnQiLCJsZXR0ZXJzcGFjaW5nIjoibm9ybWFsIiwid29yZHNwYWNpbmciOiJub3JtYWwiLCJ0ZXh0dHJhbnNmb3JtIjoibm9uZSJ9LHsiZXh0cmEiOiIifV19',
                        'slider-outer-css'   => '',
                        'slider-inner-css'   => 'box-shadow: 0 1px 3px 1px RGBA(0, 0, 0, .3) inset;',
                        'slider-title-css'   => 'box-shadow: 0 0 0 1px RGBA(255, 255, 255, .05) inset, 0 0 2px 1px RGBA(0, 0, 0, .3);'
                    )
                ),
                'light' => array(
                    'label'    => n2_('Light'),
                    'settings' => array(
                        'tab-normal-color'   => 'e9e8e0',
                        'outer-border'       => 6,
                        'outer-border-color' => 'e9e8e0',
                        'inner-border'       => 6,
                        'inner-border-color' => 'd2d0c8ff',
                        'title-font'         => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siZXh0cmEiOiJ0ZXh0LXRyYW5zZm9ybTogdXBwZXJjYXNlOyIsImNvbG9yIjoiNGQ0ZDRkZmYiLCJzaXplIjoiMTR8fHB4IiwidHNoYWRvdyI6IjB8KnwwfCp8MHwqfDAwMDAwMGZmIiwiYWZvbnQiOiJNb250c2VycmF0IiwibGluZWhlaWdodCI6IjEuMyIsImJvbGQiOjAsIml0YWxpYyI6MCwidW5kZXJsaW5lIjowLCJhbGlnbiI6ImxlZnQiLCJsZXR0ZXJzcGFjaW5nIjoibm9ybWFsIiwid29yZHNwYWNpbmciOiJub3JtYWwiLCJ0ZXh0dHJhbnNmb3JtIjoibm9uZSJ9LHsiZXh0cmEiOiIiLCJjb2xvciI6ImZmZmZmZmZmIn1dfQ==',
                        'slider-outer-css'   => 'box-shadow: 0 0 0 1px #cccccc inset;',
                        'slider-inner-css'   => 'box-shadow: 0 1px 3px 1px RGBA(0, 0, 0, .2) inset;',
                        'slider-title-css'   => 'box-shadow: 0 0 2px 1px RGBA(0, 0, 0, .2);'
                    )
                )
            )
        ));

        new N2ElementRadio($settings, 'orientation', n2_('Orientation'), 'horizontal', array(
            'options' => array(
                'horizontal' => n2_('Horizontal'),
                'vertical'   => n2_('Vertical')
            )
        ));

        new N2ElementOnOff($settings, 'carousel', n2_('Carousel'), 1, array(
            'tip' => n2_('If you turn off this option, you can\'t switch to the first slide from the last one.')
        ));

        $border = new N2ElementGroup($settings, 'slider-border', n2_('Border'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementNumberAutocomplete($border, 'outer-border', n2_('Outer width'), 6, array(
            'unit'   => 'px',
            'style'  => 'width:20px;',
            'values' => array(
                0,
                6
            )
        ));
        new N2ElementColor($border, 'outer-border-color', n2_('Outer color'), '3E3E3Eff', array(
            'alpha' => true
        ));
        new N2ElementNumberAutocomplete($border, 'inner-border', n2_('Inner width'), 6, array(
            'unit'   => 'px',
            'style'  => 'width:20px;',
            'values' => array(
                0,
                6
            )
        ));
        new N2ElementColor($border, 'inner-border-color', n2_('Inner color'), '222222ff', array(
            'alpha' => true
        ));
        new N2ElementNumber($border, 'border-radius', n2_('Border radius'), 6, array(
            'unit'  => 'px',
            'style' => 'width:15px;'
        ));

        $tabBackground = new N2ElementGroup($settings, 'tab-background', n2_('Tab background'));
        new N2ElementColor($tabBackground, 'tab-normal-color', n2_('Normal'), '3E3E3E');
        new N2ElementColor($tabBackground, 'tab-active-color', n2_('Active'), '87B801');

        new N2ElementNumberAutocomplete($settings, 'slide-margin', n2_('Slide margin'), 2, array(
            'unit'   => 'px',
            'style'  => 'width:20px;',
            'values' => array(
                0,
                2
            )
        ));

        $title = new N2ElementGroup($settings, 'slider-accordion-title', n2_('Title'));

        new N2ElementNumberAutocomplete($title, 'title-size', n2_('Size'), 30, array(
            'unit'   => 'px',
            'style'  => 'width:20px;',
            'values' => array(
                30
            )
        ));

        new N2ElementNumberAutocomplete($title, 'title-margin', n2_('Margin'), 10, array(
            'unit'     => 'px',
            'style'    => 'width:20px;',
            'values'   => array(
                10
            ),
            'rowClass' => 'n2-expert'
        ));

        new N2ElementNumberAutocomplete($title, 'title-border-radius', n2_('Border radius'), 2, array(
            'unit'     => 'px',
            'style'    => 'width:15px;',
            'values'   => array(
                0,
                2
            ),
            'rowClass' => 'n2-expert'
        ));

        new N2ElementFont($title, 'title-font', n2_('Font'), 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siZXh0cmEiOiJ0ZXh0LXRyYW5zZm9ybTogdXBwZXJjYXNlOyIsImNvbG9yIjoiZmZmZmZmZmYiLCJzaXplIjoiMTR8fHB4IiwidHNoYWRvdyI6IjB8KnwwfCp8MHwqfDAwMDAwMGZmIiwiYWZvbnQiOiJNb250c2VycmF0IiwibGluZWhlaWdodCI6IjEuMyIsImJvbGQiOjAsIml0YWxpYyI6MCwidW5kZXJsaW5lIjowLCJhbGlnbiI6ImxlZnQiLCJsZXR0ZXJzcGFjaW5nIjoibm9ybWFsIiwid29yZHNwYWNpbmciOiJub3JtYWwiLCJ0ZXh0dHJhbnNmb3JtIjoibm9uZSJ9LHsiZXh0cmEiOiIifV19', array(
            'previewMode' => 'accordionslidetitle'
        ));


        $css = new N2ElementGroup($settings, 'slider-css', 'CSS', array(
            'rowClass' => 'n2-expert'
        ));

        new N2ElementTextarea($css, 'slider-outer-css', n2_('Outer') . ' CSS', '', array(
            'fieldStyle' => 'width: 500px;resize: vertical;'
        ));

        new N2ElementTextarea($css, 'slider-inner-css', n2_('Inner') . ' CSS', 'box-shadow: 0 1px 3px 1px RGBA(0, 0, 0, .3) inset;', array(
            'fieldStyle' => 'width: 500px;resize: vertical;'
        ));

        new N2ElementTextarea($css, 'slider-title-css', n2_('Title') . ' CSS', 'box-shadow: 0 0 0 1px RGBA(255, 255, 255, .05) inset, 0 0 2px 1px RGBA(0, 0, 0, .3);', array(
            'fieldStyle' => 'width: 500px;resize: vertical;'
        ));


        $animationSettings = new N2Tab($form, 'animation-settings', n2_('Accordion slider type') . ' - ' . n2_('Animation'));

        $animation = new N2ElementGroup($animationSettings, 'slider-mainanimation', n2_('Animation'));

        new N2ElementNumberAutocomplete($animation, 'animation-duration', n2_('Duration'), 1000, array(
            'unit'   => 'ms',
            'style'  => 'width:30px;',
            'values' => array(
                800,
                1000,
                1500,
                2000
            )
        ));

        new N2ElementEasing($animation, 'animation-easing', n2_('Easing'), 'easeOutQuad', array(
            'rowClass' => 'n2-expert'
        ));
    }

    public function export($export, $slider) {
        $export->addVisual($slider['params']->get('title-font', ''));
    }

    public function import($import, $slider) {
        $slider['params']->set('title-font', $import->fixSection($slider['params']->get('title-font', '')));
    }
}

N2SSPluginSliderType::addSliderType(new N2SSPluginTypeAccordion);
