<?php

N2Loader::import('libraries.form.elements.list');
N2Loader::import('libraries.parse.parse');

class N2ElementFlickrGalleries extends N2ElementList {

    /** @var  DPZFlickr */
    protected $api;

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $result = $this->api->galleries_getList('');

        if (isset($result['galleries']) && isset($result['galleries']['gallery'])) {
            $galleries = $result['galleries']['gallery'];

            if (count($galleries)) {
                foreach ($galleries AS $gallery) {
                    $this->options[$gallery['id']] = $gallery['title']['_content'];
                }
                if ($this->getValue() == '') {
                    $this->setValue($galleries[0]['id']);
                }
            }
        }

    }

    public function setApi($api) {
        $this->api = $api;
    }
}
