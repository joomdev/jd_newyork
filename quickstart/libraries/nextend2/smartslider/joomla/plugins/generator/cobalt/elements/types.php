<?php

N2Loader::import('libraries.form.element.list');

class N2ElementCobaltTypes extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model("js_res_types");
        $types = $model->db->queryAll("SELECT id, name FROM #__js_res_types ORDER BY name ASC", false, "object");

        if (count($types)) {
            foreach ($types AS $option) {
                $this->options[$option->id] = $option->name;
            }
            if ($this->getValue() == '') {
                $this->setValue($types[0]->id);
            }
        } else {
            $this->options[''] = 'Cobalt types';
        }
    }
}
