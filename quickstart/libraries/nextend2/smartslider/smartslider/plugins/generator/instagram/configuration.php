<?php

class N2SliderGeneratorInstagramConfiguration {

    private $data;

    /** @var N2SSPluginGeneratorInstagram */
    protected $group;

    /**
     * N2SliderGeneratorInstagramConfiguration constructor.
     *
     * @param N2SSPluginGeneratorInstagram $group
     */
    public function __construct($group) {
        $this->group = $group;
        $this->data  = new N2Data(array(
            'client_id'     => '',
            'client_secret' => '',
            'access_token'  => ''
        ));

        $this->data->loadJSON(N2Base::getApplication('smartslider')->storage->get('instagram'));

    }

    public function wellConfigured() {
        if (!$this->data->get('client_id') || !$this->data->get('client_secret') || !$this->data->get('access_token')) {
            return false;
        }
        try {
            $client = $this->getApi();
            $feed   = json_decode($client->getUserFeed(), true);
            if ($feed['meta']['code'] != 200) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getApi($needRedirectUrl = false) {
        require_once(dirname(__FILE__) . "/api/Instagram.php");

        $config = array(
            'client_id'     => $this->data->get('client_id'),
            'client_secret' => $this->data->get('client_secret'),
            'redirect_uri'  => '',
            'grant_type'    => 'authorization_code',
            'scope'         => 'basic public_content'
        );
        if ($needRedirectUrl) {
            $config['redirect_uri'] = N2Base::getApplication('smartslider')->router->createUrl(array(
                "generator/finishAuth",
                array(
                    'group' => N2Request::getVar('group'),
                    'type'  => N2Request::getVar('type')
                )
            ));
        }

        $client = new Instagram($config);

        if (!$needRedirectUrl) {
            $accessToken = $this->data->get('access_token');
            if ($accessToken) {
                $client->setAccessToken($accessToken);
            }
        }

        return $client;
    }

    public function getData() {
        return $this->data->toArray();
    }

    public function addData($data, $store = true) {
        $this->data->loadArray($data);
        if ($store) {
            N2Base::getApplication('smartslider')->storage->set('instagram', null, json_encode($this->data->toArray()));
        }
    }

    public function render() {
        $this->group->loadElements();

        $form = new N2Form();
        $form->loadArray($this->getData());

        $settings = new N2Tab($form, 'instagram-generator', 'Instagram api');
        new N2ElementText($settings, 'client_id', 'Client ID', '', array(
            'style' => 'width:250px;'
        ));
        new N2ElementText($settings, 'client_secret', 'Client secret', '', array(
            'style' => 'width:250px;'
        ));
        new N2ElementInstagramToken($settings, 'access_token', n2_('Token'));
        new N2ElementContainer($settings, 'callback', n2_('Callback url'));
        new N2ElementToken($settings);

        echo $form->render('generator');

        if ($this->data->get('client_id') != '' && $this->data->get('client_secret') != '') {
            try {
                $client = $this->getApi();
                $feed   = json_decode($client->getUserFeed(), true);
                if ($feed['meta']['code'] != 200) {
                    N2Message::error($feed['meta']['error_message']);
                }
            } catch (Exception $e) {
                N2Message::error($e->getMessage());
            }
        }
    }

    public function startAuth() {
        if (session_id() == "") {
            session_start();
        }
        $this->addData(N2Request::getVar('generator'), false);

        $_SESSION['data'] = $this->getData();

        return $this->getApi(true)
                    ->getAuthorizationUrl();
    }

    public function finishAuth() {
        if (session_id() == "") {
            session_start();
        }
        $this->addData($_SESSION['data'], false);
        unset($_SESSION['data']);
        try {
            $client      = $this->getApi(true);
            $accessToken = $client->getAccessToken();
            if ($accessToken) {
                $data                 = $this->getData();
                $data['access_token'] = $accessToken;
                $this->addData($data);

                return true;
            }

            return false;
        } catch (Exception $e) {
            return $e;
        }
    }

}