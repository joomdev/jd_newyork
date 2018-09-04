<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJomSocialGroups extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);


        $model = new N2Model('community_groups');

        $query  = "SELECT id, name FROM #__community_groups ORDER BY id";
        $groups = $model->db->queryAll($query, false, "object");

        $this->options['0'] = n2_('All');

        if (count($groups)) {
            foreach ($groups AS $group) {
                $this->options[$group->id] = $group->name;
            }
        }
    }
}