<?php

N2Loader::import('libraries.form.element.list');

class N2ElementFlexiTags extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $db = JFactory::getDBO();

        $db->setQuery('SELECT id, name FROM #__flexicontent_tags WHERE published = 1 ORDER BY id');
        $menuItems = $db->loadObjectList();

        $this->options['0'] = n2_('All');

        if (count($menuItems)) {
            foreach ($menuItems AS $option) {
                $this->options[$option->id] = $option->name;
            }
        }
    }
}
