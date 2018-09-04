<?php
N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryVideo extends N2SSPluginItemFactoryAbstract {

    public $type = 'video';

    protected $layerProperties = array(
        "desktopportraitwidth"  => 300,
        "desktopportraitheight" => 180
    );

    protected $priority = 20;

    protected $class = 'N2SSItemVideo';

    public function __construct() {
        $this->title = n2_x('Video', 'Slide item');
        $this->group = n2_('Media');
    }

    /**
     * @return array
     */
    function getValues() {
        return array(
            'autoplay'     => 0,
            'video_mp4'    => '',
            'scroll-pause' => 'partly-visible',
            'showcontrols' => 1,
            'volume'       => 1,
            'autoplay'     => 0,
            'center'       => 0,
            'loop'         => 0,
            'reset'        => 0,
            'videoplay'    => '',
            'videopause'   => '',
            'videoend'     => ''
        );
    }

    /**
     * @return string
     */
    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public static function getFilled($slide, $data) {
        $data->set('poster', $slide->fill($data->get('poster', '')));
        $data->set('video_mp4', $slide->fill($data->get('video_mp4', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addImage($data->get('poster'));
        $export->addImage($data->get('video_mp4'));
    }

    public function prepareImport($import, $data) {
        $data->set('poster', $import->fixImage($data->get('poster')));
        $data->set('video_mp4', $import->fixImage($data->get('video_mp4')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('poster', N2ImageHelper::fixed($data->get('poster')));
        $data->set('video_mp4', N2ImageHelper::fixed($data->get('video_mp4')));

        return $data;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-video');

        new N2ElementVideo($settings, 'video_mp4', 'MP4 video', '', array(
            'style' => 'width:236px;'
        ));

        new N2ElementImage($settings, 'poster', n2_('Cover image'), '', array(
            'fixed' => true,
            'style' => 'width:236px;'
        ));

        new N2ElementList($settings, 'scroll-pause', n2_('Pause on scroll'), 'partly-visible', array(
            'options' => array(
                ''               => n2_('Never'),
                'partly-visible' => n2_('When partly visible'),
                'not-visible'    => n2_('When not visible'),
            )
        ));

        $misc = new N2ElementGroup($settings, 'item-video-misc');
        new N2ElementList($misc, 'volume', n2_('Volume'), 1, array(
            'options' => array(
                '0'    => n2_('Mute'),
                '0.25' => '25%',
                '0.5'  => '50%',
                '0.75' => '75%',
                '1'    => '100%'
            )
        ));
        new N2ElementList($misc, 'preload', n2_('Preload'), '', array(
            'options' => array(
                'auto'     => 'Auto',
                'metadata' => 'metadata',
                'none'     => n2_('None')
            )
        ));
        new N2ElementOnOff($misc, 'showcontrols', n2_('Controls'), 0);
        new N2ElementOnOff($misc, 'autoplay', n2_('Autoplay'), 0);
        new N2ElementOnOff($misc, 'center', n2_('Centered'), 0);
        new N2ElementOnOff($misc, 'loop', n2_('Loop'), 0);
        new N2ElementOnOff($misc, 'reset', n2_('Reset when slide changes'), 0);
    }

}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryVideo);
