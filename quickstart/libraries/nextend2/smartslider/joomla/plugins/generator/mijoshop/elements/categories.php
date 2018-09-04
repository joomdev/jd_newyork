<?php

N2Loader::import('libraries.form.element.list');

class N2ElementMijoShopCategories extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model("mijoshop_category");

        $lang   = '';
        $config = MijoShop::get('opencart')
                          ->get('config');
        if (is_object($config)) {
            $lang = ' AND cd.language_id = ' . $config->get('config_language_id');
        }

        $query = 'SELECT 
                    m.category_id AS id, 
                    cd.name AS name, 
                    cd.name AS title, 
                    m.parent_id AS parent, 
                    m.parent_id as parent_id
                FROM #__mijoshop_category m
                LEFT JOIN #__mijoshop_category_description AS cd ON cd.category_id = m.category_id
                WHERE m.status = 1 ' . $lang . '
                ORDER BY m.sort_order';

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

        $this->options['0'] = n2_('All');

        if (count($options)) {
            foreach ($options AS $option) {
                $this->options[$option->id] = $option->treename;
            }
        }

    }

}
