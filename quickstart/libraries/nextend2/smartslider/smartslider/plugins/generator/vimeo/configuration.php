<?php

class N2SliderGeneratorVimeoConfiguration {

    private $data;

    /** @var  N2SSPluginGeneratorVimeo */
    protected $group;

    /**
     * N2SliderGeneratorVimeoConfiguration constructor.
     *
     * @param N2SSPluginGeneratorVimeo $group
     */
    public function __construct($group) {
        $this->group = $group;
        $this->data  = new N2Data(array(
            'client_id'     => '',
            'client_secret' => '',
            'access_token'  => ''
        ));

        $this->data->loadJSON(N2Base::getApplication('smartslider')->storage->get('vimeo'));

    }

    public function wellConfigured() {
        if (!$this->data->get('client_id') || !$this->data->get('client_secret') || !$this->data->get('access_token')) {
            return false;
        }
        $client = $this->getApi();

        $response = $client->request('/oauth/verify');
        if ($response['status'] == 200) {
            return true;
        }

        return false;
    }

    /**
     *
     * @return \Vimeo\Vimeo
     */
    public function getApi() {

        require_once(dirname(__FILE__) . "/api/Exceptions/ExceptionInterface.php");
        require_once(dirname(__FILE__) . "/api/Exceptions/VimeoRequestException.php");
        require_once(dirname(__FILE__) . "/api/Exceptions/VimeoUploadException.php");
        require_once(dirname(__FILE__) . "/api/Vimeo.php");

        $client = new \Vimeo\Vimeo($this->data->get('client_id'), $this->data->get('client_secret'));

        $client->clientCredentials('private');

        $client->setToken($this->data->get('access_token'));

        return $client;
    }

    public function getData() {
        return $this->data->toArray();
    }

    public function addData($data, $store = true) {
        $this->data->loadArray($data);
        if ($store) {
            N2Base::getApplication('smartslider')->storage->set('vimeo', null, json_encode($this->data->toArray()));
        }
    }

    public function render() {
        $this->group->loadElements();

        $form = new N2Form();
        $form->loadArray($this->getData());

        $settings = new N2Tab($form, 'vimeo-generator', 'Vimeo api');
        new N2ElementText($settings, 'client_id', 'Client Identifier', '', array(
            'style' => 'width:400px;'
        ));
        new N2ElementText($settings, 'client_secret', 'Client secret', '', array(
            'style' => 'width:400px;'
        ));
        new N2ElementVimeoToken($settings, 'access_token', n2_('Token'));
        new N2ElementContainer($settings, 'callback', n2_('Callback url'));
        new N2ElementToken($settings);

        echo $form->render('generator');

        if ($this->data->get('client_id') != '' && $this->data->get('client_secret') != '') {
            try {
                $client = $this->getApi();

                $response = $client->request('/oauth/verify');
                if ($response['status'] != 200) {
                    if (!empty($response['body']['error'])) {
                        N2Message::error($response['body']['error']);
                    }
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

        $_SESSION['data']       = $this->getData();
        $_SESSION['vimeostate'] = rand();

        $client = $this->getApi();

        return $client->buildAuthorizationEndpoint(N2Base::getApplication('smartslider')->router->createUrl(array(
            "generator/finishauth",
            array(
                'group' => 'vimeo'
            )
        )), array('private'), $_SESSION['vimeostate']);
    }

    public function finishAuth() {
        if (session_id() == "") {
            session_start();
        }
        if (isset($_REQUEST['state']) && isset($_SESSION['vimeostate']) && $_REQUEST['state'] == $_SESSION['vimeostate']) {
            $this->addData($_SESSION['data'], false);
            unset($_SESSION['data']);
            unset($_SESSION['vimeostate']);
            try {
                $client = $this->getApi();
                $client->setToken('');
                $response = $client->accessToken($_REQUEST['code'], N2Base::getApplication('smartslider')->router->createUrl(array(
                    "generator/finishauth",
                    array(
                        'group' => 'vimeo'
                    )
                )));

                if ($response['status'] == 200) {
                    $this->data->set('access_token', $response['body']['access_token']);
                    $client->setToken($response['body']['access_token']);
                    $this->addData($this->getData());

                    return true;
                } else {
                    return $client->response['body']['error_description'];
                }
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else {
            return 'State does not match!';
        }
    }

}