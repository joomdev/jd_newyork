<?php

N2Loader::import('libraries.form.element.list');

class N2ElementDJClassifiedsLocations extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $db = JFactory::getDBO();
        $query = 'SELECT
                    id, 
                    name AS title, 
                    name, 
                    parent_id AS parent, 
                    parent_id
                FROM #__djcf_regions
                ORDER BY id';

        $db->setQuery($query);
        $menuItems = $db->loadObjectList();
        $children  = array();
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
        if ($this->getValue() == '') {
            reset($this->options);
            $this->setValue(key($this->options));
        }
    }
}