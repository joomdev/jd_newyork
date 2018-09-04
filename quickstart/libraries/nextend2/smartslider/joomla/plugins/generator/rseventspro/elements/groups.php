<?php

N2Loader::import('libraries.form.element.list');

class N2ElementRSEventsProGroups extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);
        $model  = new N2Model('rseventspro_groups');
        $query  = "SELECT id, name FROM #__rseventspro_groups";
        $groups = $model->db->queryAll($query, false, "object");

        $this->options['0'] = n2_('All');

        if (count($groups)) {
            foreach ($groups AS $group) {
                $this->options[$group->id] = $group->name;
            }
        }
    }

}
