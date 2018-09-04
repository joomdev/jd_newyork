<?php

N2Loader::import('libraries.form.element.list');

class N2ElementHikaShopTags extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);
        $model = new N2Model('tags');

        $query = "SELECT title, id FROM #__tags WHERE published = 1 AND parent_id <> 0";

        $tags = $model->db->queryAll($query, false, "object");

        $this->options['0'] = n2_('All');

        if (count($tags)) {
            foreach ($tags AS $tag) {
                $this->options[$tag->id] = $tag->title;
            }
        }
    }

}
