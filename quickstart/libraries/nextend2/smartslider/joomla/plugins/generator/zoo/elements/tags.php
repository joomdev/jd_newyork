<?php

N2Loader::import('libraries.form.element.list');

class N2ElementZooTags extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model('zoo_tag');
        $query = 'SELECT name FROM #__zoo_tag GROUP BY name';
        $tags  = $model->db->queryAll($query, false, "object");

        $this->options['0'] = n2_('All');

        if (count($tags)) {
            foreach ($tags AS $tag) {
                $this->options["'" . htmlspecialchars($tag->name) . "'"] = " - " . $tag->name;
            }
        }
    }

}
