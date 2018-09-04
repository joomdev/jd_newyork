<?php

class N2SliderGeneratorFlickrConfiguration {

    private $data;

    /** @var N2SliderGeneratorPluginAbstract */
    protected $group;

    /**
     * N2SliderGeneratorFlickrConfiguration constructor.
     *
     * @param N2SliderGeneratorPluginAbstract $group
     */
    public function __construct($group) {
        $this->group = $group;
        $this->data  = new N2Data(array(
            'api_key'    => '',
            'api_secret' => '',
            'token'      => ''
        ));

        $this->data->loadJSON(N2Base::getApplication('smartslider')->storage->get('flickr'));
    }

    public function wellConfigured() {
        if (!$this->data->get('api_key') || !$this->data->get('api_secret') || !$this->data->get('token')) {
            return false;
        }
        $api = $this->getApi();

        if ($api->call('flickr.test.login') === false) {
            return false;
        }

        return true;
    }


    public function getApi() {
        require_once(dirname(__FILE__) . "/api/DPZFlickr.php");

        $api_key    = $this->data->get('api_key');
        $api_secret = $this->data->get('api_secret');

        $api = new DPZFlickr($api_key, $api_secret, N2Base::getApplication('smartslider')->router->createUrl(array(
            "generator/finishAuth",
            array(
                'group' => N2Request::getVar('group'),
                'type'  => N2Request::getVar('type')
            )
        )));
		
        $token = json_decode($this->data->get('token'), true);
        $api->setData($token);

        return $api;
    }

    public function getData() {
        return $this->data->toArray();
    }

    public function addData($data, $store = true) {
        $this->data->loadArray($data);
        if ($store) {
            N2Base::getApplication('smartslider')->storage->set('flickr', null, json_encode($this->data->toArray()));
        }
    }

    public function render() {
        $this->group->loadElements();

        $form = new N2Form();
        $form->loadArray($this->getData());


        $settings = new N2Tab($form, 'flickr-api', 'Flick api');
        new N2ElementText($settings, 'api_key', 'Api key', '', array(
            'style' => 'width:250px;'
        ));
        new N2ElementText($settings, 'api_secret', 'Api secret', '', array(
            'style' => 'width:250px;'
        ));
        new N2ElementFlickrToken($settings, 'token', n2_('Token'));
        new N2ElementContainer($settings, 'callback', n2_('Callback url'));
        new N2ElementToken($settings);

        $api = $this->getApi();
        if ($api->call('flickr.test.login') === false) {
            N2Message::error(n2_('The key and secret is not valid!'));
        }

        echo $form->render('generator');
    }

    public function startAuth() {
        if (session_id() == "") {
            session_start();
        }
        $this->addData(N2Request::getVar('generator'), false);

        $_SESSION['data'] = $this->getData();
        $api              = $this->getApi();
        $api->setData(array());

        $url = $api->authenticate();

        if (!$url) {
            throw new Exception('Api key or Api secret is not valid.');
        }

        return $url;
    }

    public function finishAuth() {
        if (session_id() == "") {
            session_start();
        }

        $api = $this->getApi();
        $api->setData(array());
        $api->authenticateStep2();

        $this->data->loadArray($_SESSION['data']);
        $data          = $this->getData();
        $data['token'] = json_encode(array(
            'oauth_request_token'        => $api->getOauthData('oauth_request_token'),
            'oauth_request_token_secret' => $api->getOauthData('oauth_request_token_secret'),
            'oauth_access_token'         => $api->getOauthData('oauth_access_token'),
            'oauth_access_token_secret'  => $api->getOauthData('oauth_access_token_secret'),
            'user_nsid'                  => $api->getOauthData('user_nsid')
        ));

        $this->addData($data);

        unset($_SESSION['FlickrSessionOauthData']);
        unset($_SESSION['data']);

        return true;
    }

}
