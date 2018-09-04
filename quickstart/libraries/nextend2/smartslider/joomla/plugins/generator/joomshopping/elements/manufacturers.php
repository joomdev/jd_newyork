<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJoomShoppingManufacturers extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $db = JFactory::getDBO();

        require_once(JPATH_SITE . "/components/com_jshopping/lib/factory.php");
        $lang = JSFactory::getLang();

        $query = "SELECT manufacturer_id AS id, `" . $lang->get('name') . "` AS title
              FROM #__jshopping_manufacturers
              WHERE manufacturer_publish = 1
              ORDER BY ordering";

        $db->setQuery($query);
        $menuItems = $db->loadObjectList();

        $this->options['0'] = n2_('All');

        if (count($menuItems)) {
            foreach ($menuItems AS $option) {
                $this->options[$option->id] = $option->title;
            }
        }
    }

}
