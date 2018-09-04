<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJoomShoppingLabels extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $db = JFactory::getDBO();

        require_once(JPATH_SITE . "/components/com_jshopping/lib/factory.php");
        $lang = JSFactory::getLang();

        $query = "SELECT id, `" . $lang->get('name') . "` AS name
              FROM #__jshopping_product_labels
              ORDER BY name";

        $db->setQuery($query);
        $labels = $db->loadObjectList();

        $this->options['-1'] = n2_('All');
        $this->options['0']  = n2_('None');

        if (count($labels)) {
            foreach ($labels AS $option) {
                $this->options[$option->id] = $option->name;
            }
        }
    }

}
