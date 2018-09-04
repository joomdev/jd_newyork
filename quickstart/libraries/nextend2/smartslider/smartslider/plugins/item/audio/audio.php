<?php
N2Loader::import('libraries.renderable.layers.item.itemFactoryAbstract', 'smartslider');

class N2SSPluginItemFactoryAudio extends N2SSPluginItemFactoryAbstract {

    public $type = 'audio';

    protected $layerProperties = array(
        "desktopportraitwidth" => 300
    );

    protected $priority = 21;

    protected $class = 'N2SSItemAudio';

    public function __construct() {
        $this->title = n2_x('Audio', 'Slide item');
        $this->group = n2_('Media');
    }

    /**
     * @return array
     */
    function getValues() {
        return array(
            'audio_mp3'     => '',
            'volume'        => 1,
            'autoplay'      => 0,
            'loop'          => 0,
            'reset'         => 0,
            'color'         => '000000B2',
            'color2'        => 'ffffff',
            'videoplay'     => '',
            'videopause'    => '',
            'videoend'      => '',
            'fullwidth'     => 1,
            'show'          => 1,
            'show-progress' => 1,
            'show-time'     => 1,
            'show-volume'   => 1
        );
    }

    /**
     * @return string
     */
    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public static function getFilled($slide, $data) {
        $data->set('audio', $slide->fill($data->get('audio', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        $export->addImage($data->get('audio'));
    }

    public function prepareImport($import, $data) {
        $data->set('audio', $import->fixImage($data->get('audio')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('audio', N2ImageHelper::fixed($data->get('audio')));

        return $data;
    }

    public function loadResources($renderable) {
        parent::loadResources($renderable);

        $renderable->addLess($this->getPath() . "/audio.n2less", array(
            "sliderid" => $renderable->elementId
        ));
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'item-audio');

        new N2ElementVideo($settings, 'audio_mp3', n2_('MP3 audio'), '', array(
            'style' => 'width:236px;'
        ));

        $audioSettings = new N2ElementGroup($settings, 'item-audio-settings');
        new N2ElementList($audioSettings, 'volume', n2_('Volume'), 1, array(
            'options' => array(
                '0'    => n2_('Mute'),
                '0.25' => '25%',
                '0.5'  => '50%',
                '0.75' => '75%',
                '1'    => '100%'
            )
        ));
        new N2ElementOnOff($audioSettings, 'autoplay', n2_('Autoplay'), 0);
        new N2ElementOnOff($audioSettings, 'loop', n2_('Loop'), 0);
        new N2ElementOnOff($audioSettings, 'reset', n2_('Reset when slide changes'), 0);

        $colors = new N2ElementGroup($settings, 'item-audio-colors');
        new N2ElementColor($colors, 'color', n2_('Main color'), '', array(
            'alpha' => true
        ));
        new N2ElementColor($colors, 'color2', n2_('Secondary color'));

        $ui = new N2ElementGroup($settings, 'item-audio-ui');
        new N2ElementOnOff($ui, 'fullwidth', n2_('Full width'), 0);
        new N2ElementOnOff($ui, 'show', n2_('Controls'), 0);
        new N2ElementOnOff($ui, 'show-progress', n2_('Progress'), 0);
        new N2ElementOnOff($ui, 'show-time', n2_('Time'), 0);
        new N2ElementOnOff($ui, 'show-volume', n2_('Volume'), 0);


    }

}

N2SmartSliderItemsFactory::addItem(new N2SSPluginItemFactoryAudio);
