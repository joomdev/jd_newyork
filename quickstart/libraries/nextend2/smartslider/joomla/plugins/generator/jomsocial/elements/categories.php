<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJomSocialCategories extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model('community_events_category');

        $query     = "SELECT id, parent AS parent_id, name AS title FROM #__community_events_category ORDER BY parent, id";
        $menuItems = $model->db->queryAll($query, false, "object");

        $children = array();
        if ($menuItems) {
            foreach ($menuItems as $v) {
                $pt   = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }

        jimport('joomla.html.html.menu');
        $categories = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

        $this->options['0'] = n2_('All');

        if (count($categories)) {
            foreach ($categories AS $category) {
                $this->options[$category->id] = $category->treename;
            }
        }

    }
}