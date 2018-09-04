<?php

N2Loader::import('libraries.form.element.list');

class N2ElementDJClassifiedsTypes extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model('djcf_types');

        $types = $model->db->queryAll("SELECT id, name FROM #__djcf_types ORDER BY id", false, "object");

        $this->options['0'] = n2_('All');

        if (count($types)) {
            foreach ($types AS $type) {
                $this->options[$type->id] = $type->name;
            }
        }
    }
}