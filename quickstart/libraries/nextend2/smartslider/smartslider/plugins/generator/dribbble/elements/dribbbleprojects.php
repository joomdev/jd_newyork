<?php

N2Loader::import('libraries.form.elements.list');
N2Loader::import('libraries.parse.parse');

class N2ElementDribbbleProjects extends N2ElementList {

    /** @var  N2OAuth */
    protected $api;

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        try {
            $userID = $this->getForm()
                           ->get('dribbble-user-id', 'me');
            $result = null;
            if ($userID == 'me') {
                $success = $this->api->CallAPI('https://api.dribbble.com/v1/user/projects', 'GET', array('per_page' => 100), array('FailOnAccessError' => true), $result);
            } else {
                $success = $this->api->CallAPI('https://api.dribbble.com/v1/users/' . $userID . '/projects', 'GET', array('per_page' => 100), array('FailOnAccessError' => true), $result);
            }
            if (count($result)) {
                foreach ($result AS $project) {
                    $this->options[$project->id] = $project->name;
                }
                if ($this->getValue() == '') {
                    $this->setValue($result[0]->id);
                }
            }
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }
    }

    protected function fetchElement() {

        N2JS::addInline('
            new N2Classes.FormElementDribbbleProjects("' . $this->fieldID . '", "' . N2Base::getApplication('smartslider')->router->createAjaxUrl(array(
                "generator/getData",
                array(
                    'group' => N2Request::getVar('group'),
                    'type'  => N2Request::getVar('type')
                )
            )) . '");
        ');

        return parent::fetchElement();
    }

    public function setApi($api) {
        $this->api = $api;
    }
}
