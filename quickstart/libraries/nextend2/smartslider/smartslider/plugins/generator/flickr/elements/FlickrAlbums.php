<?php

N2Loader::import('libraries.form.elements.list');
N2Loader::import('libraries.parse.parse');

class N2ElementFlickrAlbums extends N2ElementList {

    /** @var  DPZFlickr */
    protected $api;

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $result = $this->api->photosets_getList('');

        if (isset($result['photosets']) && isset($result['photosets']['photoset'])) {
            $photoSets = $result['photosets']['photoset'];
            if (count($photoSets)) {
                foreach ($photoSets AS $set) {
                    $this->options[$set['id']] = $set['title']['_content'];
                }
                if ($this->getValue() == '') {
                    $this->setValue($photoSets[0]['id']);
                }
            }
        }
    }

    public function setApi($api) {
        $this->api = $api;
    }
}
