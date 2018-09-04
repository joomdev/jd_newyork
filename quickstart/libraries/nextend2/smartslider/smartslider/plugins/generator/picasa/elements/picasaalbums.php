<?php

N2Loader::import('libraries.form.elements.list');

class N2ElementPicasaAlbums extends N2ElementList {

    /** @var  Google_Client */
    protected $api;

    public function __construct(N2FormElementContainer $parent, $name = '', $label = '', $default = '', array $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);


        $http = new Google_Http_Request('https://picasaweb.google.com/data/feed/api/user/default?alt=json', 'GET', array(
            'Content-Type' => 'application/json; charset=UTF-8',
            'Accept'       => '*/*'
        ), null);

        $auth    = $this->api->getAuth();
        $request = $auth->authenticatedRequest($http);

        $code = $request->getResponseHttpCode();

        if ($code == 200) {
            $body = $request->getResponseBody();

            $data = json_decode($body, true);
            foreach ($data['feed']['entry'] as $album) {
                $value                 = str_replace('?alt=json', '', str_replace("https://picasaweb.google.com/data/entry/api", "", $album['id']['$t']));
                $this->options[$value] = $album['title']['$t'];
            }
        }
    }

    /**
     * @param Google_Client $api
     */
    public function setApi($api) {
        $this->api = $api;
    }

}