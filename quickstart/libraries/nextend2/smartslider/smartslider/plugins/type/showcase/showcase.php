<?php
class N2SSPluginTypeShowcase extends N2SSPluginSliderType {

    protected $name = 'showcase';

    public $ordering = 4;

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
    }

    public function getLabel() {
        return n2_x('Showcase', 'Slider type');
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'showcase', n2_('Showcase slider type') . ' - ' . n2_('Settings'));

        $backgroundImage = new N2ElementGroup($settings, 'slider-background-image', n2_('Slider background image'));
        new N2ElementImage($backgroundImage, 'background', n2_('Image'), '', array(
            'relatedFields' => array(
                'background-fixed',
                'background-size'
            )
        ));
        new N2ElementOnOff($backgroundImage, 'background-fixed', n2_('Fixed'), 0);
        new N2ElementTextAutocomplete($backgroundImage, 'background-size', n2_('Size'), 'cover', array(
            'rowClass' => 'n2-expert',
            'values'   => array(
                'cover',
                'contain',
                'auto'
            )
        ));


        $slideSize = new N2ElementGroup($settings, 'slide-size', n2_('Slide size'));
        new N2ElementNumberAutocomplete($slideSize, 'slide-width', n2_('Width'), 600, array(
            'values' => array(
                400,
                600,
                800,
                1000
            ),
            'unit'   => 'px',
            'style'  => 'width:30px;'
        ));
        new N2ElementNumberAutocomplete($slideSize, 'slide-height', n2_('Height'), 400, array(
            'values' => array(
                300,
                400,
                600,
                800,
                1000
            ),
            'unit'   => 'px',
            'style'  => 'width:30px;'
        ));


        $border = new N2ElementGroup($settings, 'slider-border', n2_('Border'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementNumber($border, 'border-width', n2_('Width'), 0, array(
            'unit'  => 'px',
            'style' => 'width:30px;'
        ));
        new N2ElementColor($border, 'border-color', n2_('Color'), '3E3E3Eff', array(
            'alpha' => true
        ));
        new N2ElementNumber($border, 'border-radius', n2_('Border radius'), 0, array(
            'unit'  => 'px',
            'style' => 'width:30px;'
        ));


        $slider = new N2ElementGroup($settings, 'slider-css', n2_('Slider'), array(
            'rowClass' => 'n2-expert'
        ));

        new N2ElementSkin($slider, 'slider-preset', n2_('Preset'), '', array(
            'post'    => 'break',
            'options' => array(
                'shadow'       => array(
                    'label'    => n2_('Light shadow'),
                    'settings' => array(
                        'slider-css' => 'box-shadow: 1px 0 5px RGBA(0, 0, 0, 0.2), -1px 0 5px RGBA(0, 0, 0, 0.2);'
                    )
                ),
                'shadow2'      => array(
                    'label'    => n2_('Dark shadow'),
                    'settings' => array(
                        'slider-css' => 'box-shadow: 0 2px 4px 1px rgba(0, 0, 0, 0.6);'
                    )
                ),
                'photo'        => array(
                    'label'    => n2_('Photo'),
                    'settings' => array(
                        'slider-css'   => 'box-shadow: 1px 0 5px RGBA(0, 0, 0, 0.2), -1px 0 5px RGBA(0, 0, 0, 0.2);',
                        'border-width' => '8',
                        'border-color' => 'FFFFFFFF'
                    )
                ),
                'roundedphoto' => array(
                    'label'    => n2_('Photo rounded'),
                    'settings' => array(
                        'slider-css'    => 'box-shadow: 1px 0 5px RGBA(0, 0, 0, 0.2), -1px 0 5px RGBA(0, 0, 0, 0.2);',
                        'border-width'  => '5',
                        'border-color'  => 'FFFFFFFF',
                        'border-radius' => '12'
                    )
                )
            )
        ));

        new N2ElementTextarea($slider, 'slider-css', 'CSS', '', array(
            'fieldStyle' => 'width: 500px;resize: vertical;'
        ));


        $slide = new N2ElementGroup($settings, 'slide-css', n2_('Slide'), array(
            'rowClass' => 'n2-expert'
        ));

        new N2ElementSkin($slide, 'slide-preset', n2_('Preset'), '', array(
            'post'    => 'break',
            'options' => array(
                'shadow'       => array(
                    'label'    => n2_('Light shadow'),
                    'settings' => array(
                        'slide-css' => 'box-shadow: 1px 0 5px RGBA(0, 0, 0, 0.2), -1px 0 5px RGBA(0, 0, 0, 0.2);'
                    )
                ),
                'borderradius' => array(
                    'label'    => n2_('Border radius'),
                    'settings' => array(
                        'slide-css' => "border-radius: 6px;\nbox-shadow: 1px 0 5px RGBA(0, 0, 0, 0.2), -1px 0 5px RGBA(0, 0, 0, 0.2);"
                    )
                )
            )
        ));

        new N2ElementTextarea($slide, 'slide-css', n2_('Slide') . ' CSS', '', array(
            'fieldStyle' => 'width: 500px;resize: vertical;'
        ));


        $animationSettings = new N2Tab($form, 'showcasedefaultslidertypeanimation', n2_('Showcase slider type') . ' - ' . n2_('Animation'));


        new N2ElementSkin($animationSettings, 'animation-preset', n2_('Preset'), '', array(
            'fixed'   => true,
            'options' => array(
                'none'                => array(
                    'label'    => n2_('Default'),
                    'settings' => array(
                        'slide-distance' => 60,
                        'perspective'    => 1000,
                        'opacity'        => '0|*|100|*|100|*|100',
                        'scale'          => '0|*|100|*|100|*|100',
                        'translate-x'    => '0|*|0|*|0|*|0',
                        'translate-y'    => '0|*|0|*|0|*|0',
                        'translate-z'    => '0|*|0|*|0|*|0',
                        'rotate-x'       => '0|*|0|*|0|*|0',
                        'rotate-y'       => '0|*|0|*|0|*|0',
                        'rotate-z'       => '0|*|0|*|0|*|0'
                    )
                ),
                'horizontal'          => array(
                    'label'    => n2_('Horizontal showcase'),
                    'settings' => array(
                        'animation-direction' => 'horizontal',
                        'slide-distance'      => 60,
                        'perspective'         => 1000,
                        'opacity'             => '0|*|100|*|100|*|100',
                        'scale'               => '0|*|100|*|100|*|100',
                        'translate-x'         => '0|*|0|*|0|*|0',
                        'translate-y'         => '0|*|0|*|0|*|0',
                        'translate-z'         => '0|*|0|*|0|*|0',
                        'rotate-x'            => '0|*|0|*|0|*|0',
                        'rotate-y'            => '0|*|0|*|0|*|0',
                        'rotate-z'            => '0|*|0|*|0|*|0'
                    )
                ),
                'vertical'            => array(
                    'label'    => n2_('Vertical showcase'),
                    'settings' => array(
                        'animation-direction' => 'vertical',
                        'slide-distance'      => 60,
                        'perspective'         => 1000,
                        'opacity'             => '0|*|100|*|100|*|100',
                        'scale'               => '0|*|100|*|100|*|100',
                        'translate-x'         => '0|*|0|*|0|*|0',
                        'translate-y'         => '0|*|0|*|0|*|0',
                        'translate-z'         => '0|*|0|*|0|*|0',
                        'rotate-x'            => '0|*|0|*|0|*|0',
                        'rotate-y'            => '0|*|0|*|0|*|0',
                        'rotate-z'            => '0|*|0|*|0|*|0'
                    )
                ),
                'horizontalcoverflow' => array(
                    'label'    => n2_('Horizontal cover flow'),
                    'settings' => array(
                        'animation-direction' => 'horizontal',
                        'slide-distance'      => 10,
                        'perspective'         => 2000,
                        'opacity'             => '0|*|100|*|100|*|100',
                        'scale'               => '1|*|70|*|100|*|70',
                        'translate-x'         => '0|*|0|*|0|*|0',
                        'translate-y'         => '0|*|0|*|0|*|0',
                        'translate-z'         => '0|*|0|*|0|*|0',
                        'rotate-x'            => '0|*|0|*|0|*|0',
                        'rotate-y'            => '1|*|45|*|0|*|-45',
                        'rotate-z'            => '0|*|0|*|0|*|0'
                    )
                ),
                'verticalcoverflow'   => array(
                    'label'    => n2_('Vertical cover flow'),
                    'settings' => array(
                        'animation-direction' => 'vertical',
                        'slide-distance'      => 10,
                        'perspective'         => 2000,
                        'opacity'             => '0|*|100|*|100|*|100',
                        'scale'               => '1|*|70|*|100|*|70',
                        'translate-x'         => '0|*|0|*|0|*|0',
                        'translate-y'         => '0|*|0|*|0|*|0',
                        'translate-z'         => '0|*|0|*|0|*|0',
                        'rotate-x'            => '1|*|-45|*|0|*|45',
                        'rotate-y'            => '0|*|0|*|0|*|0',
                        'rotate-z'            => '0|*|0|*|0|*|0'
                    )
                )
            )
        ));


        $properties = new N2ElementGroup($animationSettings, 'showcase-animation', n2_('Properties'));

        new N2ElementNumberAutocomplete($properties, 'animation-duration', n2_('Duration'), 800, array(
            'style'  => 'width:35px;',
            'min'    => 200,
            'values' => array(
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms'
        ));

        new N2ElementNumber($properties, 'animation-delay', n2_('Delay'), 0, array(
            'style'    => 'width:35px;',
            'min'      => 0,
            'unit'     => 'ms',
            'rowClass' => 'n2-expert'
        ));
        new N2ElementEasing($properties, 'animation-easing', n2_('Easing'), 'easeOutQuad', array(
            'rowClass' => 'n2-expert'
        ));

        new N2ElementOnOff($animationSettings, 'carousel', n2_('Carousel'), 1, array(
            'tip' => n2_('If you turn off this option, you can\'t switch to the first slide from the last one.')
        ));

        new N2ElementRadio($animationSettings, 'animation-direction', n2_('Direction'), 'horizontal', array(
            'options' => array(
                'horizontal' => n2_('Horizontal'),
                'vertical'   => n2_('Vertical')
            )
        ));

        new N2ElementNumberAutocomplete($animationSettings, 'slide-distance', n2_('Slide distance'), 60, array(
            'values' => array(
                0,
                60,
                150
            ),
            'unit'   => 'px',
            'style'  => 'width:35px;'
        ));

        new N2ElementNumberAutocomplete($animationSettings, 'perspective', n2_('Perspective'), 1000, array(
            'min'      => 0,
            'values'   => array(
                0,
                1000
            ),
            'unit'     => 'px',
            'style'    => 'width:35px;',
            'rowClass' => 'n2-expert'
        ));

        $opacity = new N2ElementMixed($animationSettings, 'opacity', n2_('Opacity'), '0|*|100|*|100|*|100', array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($opacity, 'opacity-1', n2_('Animated'));
        new N2ElementNumberAutocomplete($opacity, 'opacity-2', n2_('Before'), '', array(
            'style'  => 'width:30px;',
            'min'    => 0,
            'max'    => 100,
            'values' => array(
                0,
                70,
                100
            ),
            'unit'   => '%'
        ));
        new N2ElementNumberAutocomplete($opacity, 'opacity-3', n2_('Active'), '', array(
            'style'  => 'width:30px;',
            'min'    => 0,
            'max'    => 100,
            'values' => array(
                0,
                70,
                100
            ),
            'unit'   => '%'
        ));
        new N2ElementNumberAutocomplete($opacity, 'opacity-4', n2_('After'), '', array(
            'style'  => 'width:30px;',
            'min'    => 0,
            'max'    => 100,
            'values' => array(
                0,
                70,
                100
            ),
            'unit'   => '%'
        ));


        $scale = new N2ElementMixed($animationSettings, 'scale', n2_('Scale'), '0|*|100|*|100|*|100', array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($scale, 'scale-1', n2_('Animated'));
        new N2ElementNumberAutocomplete($scale, 'scale-2', n2_('Before'), '', array(
            'style'  => 'width:30px;',
            'min'    => 0,
            'values' => array(
                0,
                50,
                80,
                90,
                100
            ),
            'unit'   => '%'
        ));
        new N2ElementNumberAutocomplete($scale, 'scale-3', n2_('Active'), '', array(
            'style'  => 'width:30px;',
            'min'    => 0,
            'values' => array(
                0,
                50,
                80,
                90,
                100
            ),
            'unit'   => '%'
        ));
        new N2ElementNumberAutocomplete($scale, 'scale-4', n2_('After'), '', array(
            'style'  => 'width:30px;',
            'min'    => 0,
            'values' => array(
                0,
                50,
                80,
                90,
                100
            ),
            'unit'   => '%'
        ));


        $translateX = new N2ElementMixed($animationSettings, 'translate-x', 'X', '0|*|0|*|0|*|0', array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($translateX, 'translate-x-1', n2_('Animated'));
        new N2ElementNumberAutocomplete($translateX, 'translate-x-2', n2_('Before'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));
        new N2ElementNumberAutocomplete($translateX, 'translate-x-3', n2_('Active'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));
        new N2ElementNumberAutocomplete($translateX, 'translate-x-4', n2_('After'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));


        $translateY = new N2ElementMixed($animationSettings, 'translate-y', 'Y', '0|*|0|*|0|*|0', array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($translateY, 'translate-y-1', n2_('Animated'));
        new N2ElementNumberAutocomplete($translateY, 'translate-y-2', n2_('Before'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));
        new N2ElementNumberAutocomplete($translateY, 'translate-y-3', n2_('Active'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));
        new N2ElementNumberAutocomplete($translateY, 'translate-y-4', n2_('After'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));


        $translateZ = new N2ElementMixed($animationSettings, 'translate-z', 'Z', '0|*|0|*|0|*|0', array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($translateZ, 'translate-z-1', n2_('Animated'));
        new N2ElementNumberAutocomplete($translateZ, 'translate-z-2', n2_('Before'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));
        new N2ElementNumberAutocomplete($translateZ, 'translate-z-3', n2_('Active'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));
        new N2ElementNumberAutocomplete($translateZ, 'translate-z-4', n2_('After'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));


        $rotateX = new N2ElementMixed($animationSettings, 'rotate-x', n2_('Rotate') . ' X', '0|*|0|*|0|*|0', array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($rotateX, 'rotate-x-1', n2_('Animated'));
        new N2ElementNumberAutocomplete($rotateX, 'rotate-x-2', n2_('Before'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));
        new N2ElementNumberAutocomplete($rotateX, 'rotate-x-3', n2_('Active'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));
        new N2ElementNumberAutocomplete($rotateX, 'rotate-x-4', n2_('After'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));


        $rotateY = new N2ElementMixed($animationSettings, 'rotate-y', n2_('Rotate') . ' Y', '0|*|0|*|0|*|0', array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($rotateY, 'rotate-y-1', n2_('Animated'));
        new N2ElementNumberAutocomplete($rotateY, 'rotate-y-2', n2_('Before'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));
        new N2ElementNumberAutocomplete($rotateY, 'rotate-y-3', n2_('Active'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));
        new N2ElementNumberAutocomplete($rotateY, 'rotate-y-4', n2_('After'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));


        $rotateZ = new N2ElementMixed($animationSettings, 'rotate-z', n2_('Rotate') . ' Z', '0|*|0|*|0|*|0', array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($rotateZ, 'rotate-z-1', n2_('Animated'));
        new N2ElementNumberAutocomplete($rotateZ, 'rotate-z-2', n2_('Before'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));
        new N2ElementNumberAutocomplete($rotateZ, 'rotate-z-3', n2_('Active'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));
        new N2ElementNumberAutocomplete($rotateZ, 'rotate-z-4', n2_('After'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));
    }

    public function export($export, $slider) {
        $export->addImage($slider['params']->get('background', ''));
    }

    public function import($import, $slider) {

        $slider['params']->set('background', $import->fixImage($slider['params']->get('background', '')));
    }
}

N2SSPluginSliderType::addSliderType(new N2SSPluginTypeShowcase);
