<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEShopCategories extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model("eshop_categories");

        $query = 'SELECT a.id AS id, a.category_parent_id AS parent_id, b.category_name AS title
                  FROM #__eshop_categories AS a
                  LEFT JOIN #__eshop_categorydetails AS b ON a.id = b.category_id
                  WHERE a.published = 1
                  ORDER BY parent_id';

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
        $options = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

        $this->options[0] = n2_('All');

        if (count($options)) {
            foreach ($options AS $option) {
                $this->options[$option->id] = $option->treename;
            }
        }
    }

}
