<?php

class N2SSPluginTypeSimple extends N2SSPluginSliderType {

    protected $name = 'simple';

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
    }

    public function getLabel() {
        return n2_x('Simple', 'Slider type');
    }

    public function renderFields($form) {
        $animationSettings = new N2Tab($form, 'simpledefaultslidertypeanimation', n2_('Simple slider type') . ' - ' . n2_('Animation'));

        new N2ElementRadio($animationSettings, 'animation', n2_('Main animation'), 'horizontal', array(
            'options' => array(
                'no'                  => n2_('No animation'),
                'fade'                => n2_('Fade'),
                'crossfade'           => n2_('Crossfade'),
                'horizontal'          => n2_('Horizontal'),
                'vertical'            => n2_('Vertical'),
                'horizontal-reversed' => n2_('Horizontal - reversed'),
                'vertical-reversed'   => n2_('Vertical - reversed')
            )
        ));

        $mainanimationGroup = new N2ElementGroup($animationSettings, 'slider-main-animation', n2_('Main animation properties'));

        new N2ElementNumberAutocomplete($mainanimationGroup, 'animation-duration', n2_('Duration'), 800, array(
            'min'    => 0,
            'values' => array(
                800,
                1500,
                2000
            ),
            'unit'   => 'ms',
            'style'  => 'width:35px;'
        ));
        new N2ElementNumber($mainanimationGroup, 'animation-delay', n2_('Delay'), 0, array(
            'min'      => 0,
            'unit'     => 'ms',
            'style'    => 'width:35px;',
            'rowClass' => 'n2-expert'
        ));
        new N2ElementEasing($mainanimationGroup, 'animation-easing', n2_('Easing'), 'easeOutQuad', array(
            'rowClass' => 'n2-expert'
        ));

        $parallaxOverlap = $form->getForm()
                                ->get('animation-parallax-overlap', false);
        if ($parallaxOverlap === false) {
            $animationParallax = $form->getForm()
                                      ->get('animation-parallax', false);
            if ($animationParallax !== false) {
                $parallaxOverlap = 100 - floatval($animationParallax) * 100;
            } else {
                $parallaxOverlap = 0;
            }
            $form->getForm()
                 ->set('animation-parallax-overlap', $parallaxOverlap);
        }
        new N2ElementNumberAutocomplete($mainanimationGroup, 'animation-parallax-overlap', n2_('Parallax overlap'), 0, array(
            'values'   => array(
                0,
                10,
                20,
                30
            ),
            'unit'     => '%',
            'wide'     => 3,
            'rowClass' => 'n2-expert'
        ));
    

        $backgroundAnimationGroup = new N2ElementGroup($animationSettings, 'slider-background-animation', n2_('Background animation'));
        new N2ElementBackgroundAnimation($backgroundAnimationGroup, 'background-animation', n2_('Animation(s)'), '', array(
            'relatedFields' => array(
                'background-animation-color',
                'background-animation-speed',
                'animation-shifted-background-animation'
            )
        ));
        new N2ElementHidden($backgroundAnimationGroup, 'background-animation-color', '', '333333ff');

        new N2ElementList($backgroundAnimationGroup, 'background-animation-speed', n2_('Speed'), 'normal', array(
            'options' => array(
                'superSlow10' => n2_('Super slow') . ' 10x',
                'superSlow'   => n2_('Super slow') . ' 3x',
                'slow'        => n2_('Slow') . ' 1.5x',
                'normal'      => n2_('Normal') . ' 1x',
                'fast'        => n2_('Fast') . ' 0.75x.',
                'superFast'   => n2_('Super fast') . ' 0.5x'
            )
        ));
        new N2ElementRadio($backgroundAnimationGroup, 'animation-shifted-background-animation', n2_('Shifted'), 'auto', array(
            'tip'      => n2_('The background and the main animation plays simultaneously or shifted.'),
            'rowClass' => 'n2-expert',
            'options'  => array(
                'auto' => n2_('Auto'),
                '0'    => n2_('Off'),
                '1'    => n2_('On')
            )
        ));
    
        $kenBurnsGroup = new N2ElementGroup($animationSettings, 'ken-burns', n2_('Ken Burns effect'));

        new N2ElementPostBackgroundAnimation($kenBurnsGroup, 'kenburns-animation', n2_('Ken Burns effect'), '50|*|50|*|', array(
            'relatedFields' => array(
                'kenburns-animation-speed',
                'kenburns-animation-strength'
            )
        ));

        new N2ElementList($kenBurnsGroup, 'kenburns-animation-speed', n2_('Speed'), 'default', array(
            'options' => array(
                'default'   => n2_('Default'),
                'superSlow' => n2_('Super slow') . ' 0.25x',
                'slow'      => n2_('Slow') . ' 0.5x',
                'normal'    => n2_('Normal') . ' 1x',
                'fast'      => n2_('Fast') . ' 2x',
                'superFast' => n2_('Super fast' . ' 4x')
            )
        ));

        new N2ElementList($kenBurnsGroup, 'kenburns-animation-strength', n2_('Strength'), 'default', array(
            'options' => array(
                'default'     => n2_('Default'),
                'superSoft'   => n2_('Super soft') . ' 0.3x',
                'soft'        => n2_('Soft') . ' 0.6x',
                'normal'      => n2_('Normal') . ' 1x',
                'strong'      => n2_('Strong') . ' 1.5x',
                'superStrong' => n2_('Super strong') . ' 2x'
            )
        ));

    
        new N2ElementOnOff($animationSettings, 'carousel', n2_('Carousel'), 1, array(
            'tip'      => n2_('If you turn off this option, you can\'t switch to the first slide from the last one.'),
            'rowClass' => 'n2-expert'
        ));
    
        $settings = new N2Tab($form, 'simpleslidertype', n2_('Simple slider type') . ' - ' . n2_('Settings'));

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


        $backgroundVideo = new N2ElementGroup($settings, 'slider-background-video', n2_('Slider background video'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementVideo($backgroundVideo, 'backgroundVideoMp4', n2_('MP4 video'), '', array(
            'relatedFields' => array(
                'backgroundVideoMuted',
                'backgroundVideoLoop',
                'backgroundVideoMode'
            )
        ));
        new N2ElementOnOff($backgroundVideo, 'backgroundVideoMuted', n2_('Muted'), 1);
        new N2ElementOnOff($backgroundVideo, 'backgroundVideoLoop', n2_('Loop'), 1);
        new N2ElementList($backgroundVideo, 'backgroundVideoMode', n2_('Fill mode'), 'fill', array(
            'options' => array(
                'fill'   => n2_('Fill'),
                'fit'    => n2_('Fit'),
                'center' => n2_('Center')
            )
        ));


        new N2ElementOnOff($settings, 'dynamic-height', n2_('Background image dynamic height'), 0, array(
            'tip' => n2_('The height of your slides changes according to the height of the background image.')
        ));

        new N2ElementOnOff($settings, 'loop-single-slide', n2_('Loop single slide'), 0, array(
            'rowClass' => 'n2-expert',
            'tip'      => n2_('In case of one slide, it repeats the animation of the slide.')
        ));


        $padding = new N2ElementMixed($settings, 'padding', n2_('Padding'), '0|*|0|*|0|*|0', array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementNumber($padding, 'padding-1', n2_('Top'), '', array(
            'unit'  => 'px',
            'style' => 'width:25px;'
        ));
        new N2ElementNumber($padding, 'padding-2', n2_('Right'), '', array(
            'unit'  => 'px',
            'style' => 'width:25px;'
        ));
        new N2ElementNumber($padding, 'padding-3', n2_('Bottom'), '', array(
            'unit'  => 'px',
            'style' => 'width:25px;'
        ));
        new N2ElementNumber($padding, 'padding-4', n2_('Left'), '', array(
            'unit'  => 'px',
            'style' => 'width:25px;'
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


        new N2ElementTextarea($settings, 'slide-css', n2_('Slide') . ' CSS', '', array(
            'fieldStyle' => 'width: 500px;resize: vertical;',
            'rowClass'   => 'n2-expert'
        ));

    
    }

    public function renderSlideFields($form) {

        $_simpleAnimation = new N2TabGroupped($form, 'simple-animation', false);
        $simpleAnimation  = new N2Tab($_simpleAnimation, 'simple-animation-tab');

        $backgroundAnimationGroup = new N2ElementGroup($simpleAnimation, 'backgroundanimation', n2_('Background animation'));
        new N2ElementBackgroundAnimation($backgroundAnimationGroup, 'background-animation', n2_('Animation(s)'), '', array(
            'relatedFields' => array(
                'background-animation-speed'
            )
        ));

        new N2ElementList($backgroundAnimationGroup, 'background-animation-speed', n2_('Speed'), 'default', array(
            'options' => array(
                'default'     => n2_('Default'),
                'superSlow10' => n2_('Super slow') . ' 10x',
                'superSlow'   => n2_('Super slow') . ' 3x',
                'slow'        => n2_('Slow') . ' 1.5x',
                'normal'      => n2_('Normal') . ' 1x',
                'fast'        => n2_('Fast') . ' 0.75x.',
                'superFast'   => n2_('Super fast') . ' 0.5x'
            )
        ));
        $kenBurnsGroup = new N2ElementGroup($simpleAnimation, 'kenburns', n2_('Ken Burns effect'));

        new N2ElementPostBackgroundAnimation($kenBurnsGroup, 'kenburns-animation', n2_('Ken Burns effect'), '50|*|50|*|', array(
            'relatedFields' => array(
                'kenburns-animation-speed',
                'kenburns-animation-strength'
            )
        ));

        new N2ElementList($kenBurnsGroup, 'kenburns-animation-speed', n2_('Speed'), 'default', array(
            'options' => array(
                'default'   => n2_('Default'),
                'superSlow' => n2_('Super slow') . ' 0.25x',
                'slow'      => n2_('Slow') . ' 0.5x',
                'normal'    => n2_('Normal') . ' 1x',
                'fast'      => n2_('Fast') . ' 2x',
                'superFast' => n2_('Super fast' . ' 4x')
            )
        ));

        new N2ElementList($kenBurnsGroup, 'kenburns-animation-strength', n2_('Strength'), 'default', array(
            'options' => array(
                'default'     => n2_('Default'),
                'superSoft'   => n2_('Super soft') . ' 0.3x',
                'soft'        => n2_('Soft') . ' 0.6x',
                'normal'      => n2_('Normal') . ' 1x',
                'strong'      => n2_('Strong') . ' 1.5x',
                'superStrong' => n2_('Super strong') . ' 2x'
            )
        ));
    
    }

    public function export($export, $slider) {
        $export->addImage($slider['params']->get('background', ''));
        $export->addImage($slider['params']->get('backgroundVideoMp4', ''));
    }

    public function import($import, $slider) {

        $slider['params']->set('background', $import->fixImage($slider['params']->get('background', '')));
        $slider['params']->set('backgroundVideoMp4', $import->fixImage($slider['params']->get('backgroundVideoMp4', '')));
    }
}

N2SSPluginSliderType::addSliderType(new N2SSPluginTypeSimple);