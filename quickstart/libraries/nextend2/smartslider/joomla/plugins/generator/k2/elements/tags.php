<?php

N2Loader::import('libraries.form.element.list');

class N2ElementK2tags extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model('k2_tags');

        $query = 'SELECT id, name FROM #__k2_tags WHERE published = 1 ORDER BY id';

        $tags = $model->db->queryAll($query, false, "object");

        $this->options['0'] = n2_('All');

        if (count($tags)) {
            foreach ($tags AS $tag) {
                $this->options[$tag->id] = $tag->name;
            }
        }
    }

}
