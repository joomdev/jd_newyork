<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJomSocialProfiles extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);


        $model = new N2Model('community_profiles');

        $query    = "SELECT id, name FROM #__community_profiles ORDER BY id";
        $profiles = $model->db->queryAll($query, false, "object");

        $this->options['0'] = n2_('All');

        if (count($profiles)) {
            foreach ($profiles AS $profile) {
                $this->options[$profile->id] = $profile->name;
            }
        }
    }
}