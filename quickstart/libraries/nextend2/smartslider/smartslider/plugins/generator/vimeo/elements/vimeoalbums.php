<?php

N2Loader::import('libraries.form.elements.list');

class N2ElementVimeoAlbums extends N2ElementList {

    /** @var  \Vimeo\Vimeo */
    protected $api;

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);


        $response = $this->api->request('/me/albums', array(
            'per_page' => 100
        ));

        if ($response['status'] == 200) {
            $albums = $response['body']['data'];
            foreach ($albums AS $album) {
                $this->options[$album['uri']] = $album['name'];
            }

            if (!isset($this->options[$this->getValue()])) {
                $this->setValue($albums[0]['uri']);
            }
        }
    }

    /**
     * @param \Vimeo\Vimeo $api
     */
    public function setApi($api) {
        $this->api = $api;
    }

}
