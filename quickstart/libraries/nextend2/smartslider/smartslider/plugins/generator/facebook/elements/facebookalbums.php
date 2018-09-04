<?php

N2Loader::import('libraries.form.elements.list');
N2Loader::import('libraries.parse.parse');

class N2ElementFacebookAlbums extends N2ElementList {

    /** @var  \Facebook\Facebook|null */
    protected $api;

    protected function fetchElement() {
        N2JS::addInline('
            new N2Classes.FormElementFacebookAlbums("' . $this->fieldID . '", "' . N2Base::getApplication('smartslider')->router->createAjaxUrl(array(
                "generator/getData",
                array(
                    'group' => N2Request::getVar('group'),
                    'type'  => N2Request::getVar('type')
                )
            )) . '");
        ');

        try {
            $id     = $this->getForm()
                           ->get('facebook-id', 'me');
            $result = $this->api->get($id . '/albums')
                                ->getDecodedBody();
            if (count($result['data'])) {
                foreach ($result['data'] AS $album) {
                    $this->options[$album['id']] = $album['name'];
                }
                if ($this->getValue() == '') {
                    $this->setValue($result['data'][0]['id']);
                }
            }
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }

        return parent::fetchElement();
    }

    /**
     * @param \Facebook\Facebook|null $api
     */
    public function setApi($api) {
        $this->api = $api;
    }

}
