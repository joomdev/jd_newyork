<?php

class N2SliderGeneratorFacebookConfiguration {

    private $data;

    /** @var N2SliderGeneratorPluginAbstract */
    protected $group;

    /**
     * N2SliderGeneratorFacebookConfiguration constructor.
     *
     * @param N2SliderGeneratorPluginAbstract $group
     */
    public function __construct($group) {
        $this->group = $group;
        $this->data  = new N2Data(array(
            'appId'       => '',
            'secret'      => '',
            'accessToken' => ''
        ));

        $this->data->loadJSON(N2Base::getApplication('smartslider')->storage->get('facebook'));

    }

    public function wellConfigured() {
        if (!$this->data->get('appId') || !$this->data->get('secret') || !$this->data->get('accessToken')) {
            return false;
        }
        $fb = $this->getApi();
        try {
            $fb->get('/me');

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return \Facebook\Facebook|null
     */
    public function getApi() {

        if (!class_exists('Facebook')) {
            require(dirname(__FILE__) . '/Facebook/autoload.php');
        }
        $appId = $this->data->get('appId');
        if (!empty($appId) && !empty($this->data->get('secret'))) {
            $fb = new Facebook\Facebook(array(
                'app_id'     => $this->data->get('appId'),
                'app_secret' => $this->data->get('secret')
            ));

            $accessToken = $this->data->get('accessToken');
            if ($accessToken) {
                $accessToken = json_decode($accessToken);
                if (count($accessToken) == 2) {
                    $fb->setDefaultAccessToken(new \Facebook\Authentication\AccessToken($accessToken[0], $accessToken[1]));
                }
            }

            return $fb;
        } else if(!empty($appId) && empty($this->data->get('secret'))){
    		    N2Message::error(n2_('The secret is empty. Please insert that value too!'));
    		} else if(empty($appId) && !empty($this->data->get('secret'))){
            N2Message::error(n2_('The App ID is empty. Please insert that value too!'));
    		} else {

            return null;
        }
    }

    public function getData() {
        return $this->data->toArray();
    }

    public function addData($data, $store = true) {
        $this->data->loadArray($data);
        if ($store) {
            N2Base::getApplication('smartslider')->storage->set('facebook', null, json_encode($this->data->toArray()));
        }
    }

    public function render() {
        $this->group->loadElements();

        $form = new N2Form();
        $form->loadArray($this->getData());

        $settings = new N2Tab($form, 'facebook-generator', 'Facebook api');
        new N2ElementText($settings, 'appId', 'App ID', '', array(
            'style' => 'width:120px;'
        ));
        new N2ElementText($settings, 'secret', 'Secret', '', array(
            'style' => 'width:250px;'
        ));
        new N2ElementFacebookToken($settings, 'accessToken', n2_('Token'));
        new N2ElementContainer($settings, 'callback', n2_('Callback url'));
        new N2ElementToken($settings);

        echo $form->render('generator');

        $fb = $this->getApi();
        if (!empty($fb)) {
            $accessToken = $fb->getDefaultAccessToken();
        }
        if (!empty($accessToken)) {
            try {
                /** @var Facebook\Authentication\AccessTokenMetadata $result */
                $result = $fb->getOAuth2Client()
                             ->debugToken($accessToken);

                if ($result->getIsValid()) {
                    $result->validateExpiration();
                    N2Message::notice('The token will expire on ' . date('F j, Y', $result->getExpiresAt()
                                                                                          ->getTimestamp()));
                } else {
                    N2Message::error(n2_('The token expired. Please request new token! '));
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

        $api = $this->getApi();
        if ($api) {
            return $api->getRedirectLoginHelper()
                       ->getLoginUrl(N2Base::getApplication('smartslider')->router->createUrl(array(
                           "generator/finishAuth",
                           array(
                               'group' => N2Request::getVar('group'),
                               'type'  => N2Request::getVar('type')
                           )
                       )), array(
                           'user_photos'
                       ));
        }
        throw new Exception('App ID missing!');
    }

    public function finishAuth() {
        if (session_id() == "") {
            session_start();
        }
        $this->addData($_SESSION['data'], false);
        unset($_SESSION['data']);

        $fb = $this->getApi();
        try {
            $helper      = $fb->getRedirectLoginHelper();
            $accessToken = $helper->getAccessToken(N2Base::getApplication('smartslider')->router->createUrl(array(
                "generator/finishAuth",
                array(
                    'group' => N2Request::getVar('group'),
                    'type'  => N2Request::getVar('type')
                )
            )));
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                // The OAuth 2.0 client handler helps us manage access tokens
                $oAuth2Client = $fb->getOAuth2Client();
                $accessToken  = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                exit;
            }
        }

        $fb->setDefaultAccessToken($accessToken);

        try {
            $user = $fb->get('/me');
            if ($user) {
                $data                = $this->getData();
                $data['accessToken'] = json_encode(array(
                    $accessToken->getValue(),
                    $accessToken->getExpiresAt()
                                ->getTimestamp()
                ));
                $this->addData($data);

                return true;
            }

            return false;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getAlbums() {
        $ID = N2Request::getVar('facebookID');

        $api    = $this->getApi();
        $result = $api->sendRequest('GET', $ID . '/albums')
                      ->getDecodedBody();

        $albums = array();
        if (count($result['data'])) {
            foreach ($result['data'] AS $album) {
                $albums[$album['id']] = $album['name'];
            }
        }

        return $albums;
    }

}