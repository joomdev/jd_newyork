<?php

class N2SliderGeneratorDribbbleConfiguration {

    /** @var N2Data */
    private $data;

    /** @var N2SliderGeneratorPluginAbstract */
    protected $group;

    /**
     * @param N2SliderGeneratorPluginAbstract $group
     */
    public function __construct($group) {
        $this->group = $group;
        $this->data  = new N2Data(array(
            'apiKey'      => '',
            'apiSecret'   => '',
            'accessToken' => ''
        ));

        $this->data->loadJSON(N2Base::getApplication('smartslider')->storage->get('dribbble'));

    }

    public function wellConfigured() {
        if (!$this->data->get('apiKey') || !$this->data->get('apiSecret') || !$this->data->get('accessToken')) {
            return false;
        }

        $client = $this->getApi();
        try {

            $success = $client->CallAPI('https://api.dribbble.com/v1/user', 'GET', array(), array('FailOnAccessError' => true), $user);

            if (!$success) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getApi() {

        N2Loader::import('libraries.oauth.oauth');

        $client         = new N2OAuth();
        $client->server = 'Dribbble';

        $client->client_id     = $this->data->get('apiKey');
        $client->client_secret = $this->data->get('apiSecret');
        $client->access_token  = $this->data->get('accessToken', null);
        $client->scope         = 'public';
        $client->debug         = true;
        $client->debug_http    = true;

        return $client;
    }

    public function getData() {
        return $this->data->toArray();
    }

    public function addData($data, $store = true) {
        $this->data->loadArray($data);
        if ($store) {
            N2Base::getApplication('smartslider')->storage->set('dribbble', null, json_encode($this->data->toArray()));
        }
    }

    public function render() {
        $this->group->loadElements();

        $form = new N2Form();
        $form->loadArray($this->getData());

        $settings = new N2Tab($form, 'dribbble', 'Dribbble api');
        new N2ElementText($settings, 'apiKey', 'Client ID', '', array(
            'style' => 'width:450px;'
        ));
        new N2ElementText($settings, 'apiSecret', 'Client secret', '', array(
            'style' => 'width:450px;'
        ));
        new N2ElementDribbbleToken($settings, 'accessToken', n2_('Token'), '', array(
            'style' => 'width:450px;'
        ));
        new N2ElementContainer($settings, 'callback', n2_('Callback url'));
        new N2ElementToken($settings);

        echo $form->render('generator');

        try {
            $this->getApi();
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }
    }

    public function startAuth() {
        if (session_id() == "") {
            session_start();
        }
        $this->addData(N2Request::getVar('generator'), false);

        $_SESSION['data'] = $this->getData();

        $client               = $this->getApi();
        $client->redirect_uri = N2Base::getApplication('smartslider')->router->createUrl(array(
            "generator/finishAuth",
            array(
                'group' => N2Request::getVar('group')
            )
        ));

        $client->Initialize();
        if (isset($_SESSION['OAUTH_ACCESS_TOKEN'])) unset($_SESSION['OAUTH_ACCESS_TOKEN']);
        if (isset($_SESSION['OAUTH_STATE'])) unset($_SESSION['OAUTH_STATE']);
        $client->access_token = '';
        $client->CheckAccessToken($redirect_uri);

        return $redirect_uri;
    }

    public function finishAuth() {
        if (session_id() == "") {
            session_start();
        }
        $this->addData($_SESSION['data'], false);
        unset($_SESSION['data']);
        try {
            $client               = $this->getApi();
            $client->redirect_uri = N2Base::getApplication('smartslider')->router->createUrl(array(
                "generator/finishAuth",
                array(
                    'group' => N2Request::getVar('group')
                )
            ));
            $client->Initialize();
            $client->CheckAccessToken($redirect_uri);
            $accessToken = $client->access_token;

            if ($accessToken) {
                $data                = $this->getData();
                $data['accessToken'] = $accessToken;
                $this->addData($data);

                return true;
            }

            return false;
        } catch (Exception $e) {
            return $e;
        }
    }

    public function getProjects() {
        $userID = N2Request::getVar('userID');
        if ($userID == '' || $userID == 'me') {

            $success = $this->getApi()
                            ->CallAPI('https://api.dribbble.com/v1/user/projects', 'GET', array('per_page' => 100), array('FailOnAccessError' => true), $result);
        } else {
            $success = $this->getApi()
                            ->CallAPI('https://api.dribbble.com/v1/users/' . $userID . '/projects', 'GET', array('per_page' => 100), array('FailOnAccessError' => true), $result);
        }

        $projects = array();
        if (count($result)) {
            foreach ($result AS $project) {
                $projects[$project->id] = $project->name;
            }
        } else {
            $projects[''] = 'No public projects';
        }

        return $projects;
    }

}