<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEasyDiscussCategories extends N2ElementList {

    protected $isMultiple = true;
    protected $size = 10;

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model     = new N2Model('discuss_category');
        $menuItems = $model->db->queryAll('SELECT * FROM #__discuss_category WHERE published = 1 ORDER BY parent_id, ordering', false, "object");

        $children = array();
        if ($menuItems) {
            foreach ($menuItems as $v) {
                $pt   = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }

        $this->options['0'] = n2_('All');

        jimport('joomla.html.html.menu');
        $options = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
        if (count($options)) {
            foreach ($options AS $option) {
                $this->options[$option->id] = $option->treename;
            }
        }
    }
}