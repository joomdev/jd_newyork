<?php
N2Loader::import('libraries.form.element.list');
require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jcart' . DIRECTORY_SEPARATOR . 'config.php');

class N2ElementJCartCategories extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $db = JFactory::getDBO();

        $query = 'SELECT c.category_id AS id, c.parent_id AS parent_id, cd.name AS name,  cd.name AS title FROM ' . DB_PREFIX . 'category AS c
                    LEFT JOIN ' . DB_PREFIX . 'category_description AS cd ON c.category_id = cd.category_id';

        $db->setQuery($query);
        $allItems = $db->loadObjectList();
        $children = array();
        if ($allItems) {
            foreach ($allItems as $v) {
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
