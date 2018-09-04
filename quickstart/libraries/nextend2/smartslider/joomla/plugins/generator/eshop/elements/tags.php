<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEShopTags extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model("eshop_manufacturers");

        $query = 'SELECT tag_name, id
                  FROM #__eshop_tags
                  ORDER BY id';

        $tags = $model->db->queryAll($query, false, "object");

        $this->options[0] = n2_('All');

        if (count($tags)) {
            foreach ($tags AS $tag) {
                $this->options[$tag->id] = $tag->tag_name;
            }
        }
    }

}
