<?php
class N2SSPluginTypeBlock extends N2SSPluginSliderType {

    protected $name = 'block';

    public $ordering = 2;

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
    }

    public function getLabel() {
        return n2_x('Block', 'Slider type');
    }

    public function renderFields($form) {
        $animationSettings = new N2Tab($form, 'slider-type-block-animation', n2_('Block') . ' - ' . n2_('Animation'));
        $kenBurnsGroup     = new N2ElementGroup($animationSettings, 'kenburns', n2_('Ken Burns effect'));

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


        $settings = new N2Tab($form, 'slider-type-block', n2_('Block') . ' - ' . n2_('Settings'), array(
            'class' => 'n2-expert'
        ));

        $backgroundImage = new N2ElementGroup($settings, 'slider-background-image', n2_('Slider background image'));
        new N2ElementImage($backgroundImage, 'background', n2_('Image'), '', array(
            'relatedFields' => array(
                'background-fixed',
                'background-size'
            )
        ));
        new N2ElementOnOff($backgroundImage, 'background-fixed', n2_('Fixed'), 0);
        new N2ElementTextAutocomplete($backgroundImage, 'background-size', n2_('Size'), 'cover', array(
            'values' => array(
                'cover',
                'contain',
                'auto'
            )
        ));

        $backgroundVideo = new N2ElementGroup($settings, 'slider-background-video', n2_('Slider background video'));
        new N2ElementVideo($backgroundVideo, 'backgroundVideoMp4', 'MP4 video', '', array(
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

        new N2ElementTextarea($settings, 'slider-css', 'CSS', '', array(
            'fieldStyle' => 'width: 500px;resize: vertical;'
        ));
    }

    public function renderSlideFields($form) {

        $_blockAnimation = new N2TabGroupped($form, 'block-animation', false);
        $blockAnimation  = new N2Tab($_blockAnimation, 'block-animation-tab');
        $kenBurnsGroup   = new N2ElementGroup($blockAnimation, 'kenburns', n2_('Ken Burns effect'));

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

N2SSPluginSliderType::addSliderType(new N2SSPluginTypeBlock);
