<?php

N2Loader::import('libraries.form.element.list');

class N2ElementCobaltCategories extends N2ElementList {

    protected $section_id;

    protected $isMultiple = true;

    protected $size = 10;

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $db = new N2Model("js_res_sections");

        $query = "SELECT DISTINCT id, title, title AS name, parent_id, parent_id AS parent, section_id
            FROM #__js_res_categories WHERE section_id = '" . $this->section_id . "' ORDER BY lft ASC";

        $categories = $db->db->queryAll($query, false, "object");

        $children = array();
        if ($categories) {
            foreach ($categories as $v) {
                $pt   = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }

        jimport('joomla.html.html.menu');
        $options = JHTML::_('menu.treerecurse', 1, '', array(), $children, 9999, 0, 0);

        $this->options['0'] = n2_('All');

        if (count($options)) {
            foreach ($options AS $option) {
                $this->options[$option->id] = $option->treename;
            }
        }
    }

    /**
     * @param mixed $section_id
     */
    public function setSectionId($section_id) {
        $this->section_id = $section_id;
    }


}
