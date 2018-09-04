<?php

N2Loader::import('libraries.form.elements.list');

class N2ElementYoutubePlaylistByUser extends N2ElementList {

    /** @var  N2SliderGeneratorYouTubeConfiguration */
    protected $config;

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        try {
            $playlists = $this->config->getPlaylists($this->config->getApi(), $this->getForm()
                                                                                   ->get('channel-id', ''));

            foreach ($playlists AS $k => $item) {
                $this->options[$item['id']] = $item['snippet']['title'];
            }

            if (!isset($this->options[$this->getValue()])) {
                $this->setValue($playlists[0]['id']);
            }

        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }


    }

    protected function fetchElement() {

        N2JS::addInline('
            new N2Classes.FormElementYouTubePlaylists("' . $this->fieldID . '", "' . N2Base::getApplication('smartslider')->router->createAjaxUrl(array(
                "generator/getData",
                array(
                    'group' => N2Request::getVar('group'),
                    'type'  => N2Request::getVar('type')
                )
            )) . '");
        ');

        return parent::fetchElement();
    }

    /**
     * @param N2SliderGeneratorYouTubeConfiguration $config
     */
    public function setConfig($config) {
        $this->config = $config;
    }
}
