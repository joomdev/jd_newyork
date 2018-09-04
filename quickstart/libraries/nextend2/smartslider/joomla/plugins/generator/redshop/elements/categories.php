<?php

N2Loader::import('libraries.form.element.list');

class N2ElementRedShopCategories extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model('redshop_category');

        $query = 'SELECT
                m.id AS id, 
                m.name AS name, 
                m.name AS title,
                m.parent_id as parent_id
            FROM #__redshop_category m
            WHERE m.published = 1                
            ORDER BY m.id';

        $menuItems = $model->db->queryAll($query, false, "object");
        $children  = array();
        if ($menuItems) {
            foreach ($menuItems as $v) {
                $pt   = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }
        jimport('joomla.html.html.menu');
        $options = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

        $this->options['0'] = n2_('All');

        if (count($options)) {
            foreach ($options AS $option) {
                $this->options[$option->id] = $option->treename;
            }
        }
    }

}
