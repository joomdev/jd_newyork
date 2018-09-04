<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJEventsCategories extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model     = new N2Model('jevents_categories');
        $query     = "SELECT id, parent_id, title, name FROM #__assets WHERE name LIKE '%com_jevents.category%' ORDER BY parent_id";
        $menuItems = $model->db->queryAll($query, false, "object");

        $parentModel = new N2Model('jevents_categories_parent');
        $query       = "SELECT id FROM #__assets WHERE name = 'com_jevents' LIMIT 1";
        $mainParent  = $parentModel->db->queryAll($query, false, "object");

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
        $options = JHTML::_('menu.treerecurse', $mainParent[0]->id, '', array(), $children, 9999, 0, 0);

        $this->options['0'] = n2_('All');

        if (count($options)) {
            foreach ($options AS $option) {
                $id                    = explode('.', $option->name);
                $this->options[$id[2]] = $option->treename;
            }
        }

    }
}
