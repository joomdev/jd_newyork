<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJAuctionLanguages extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $db = JFactory::getDBO();

        $query = "SELECT lgid, name FROM #__language_node WHERE lgid IN (SELECT lgid FROM #__product_trans)";

        $db->setQuery($query);
        $menuItems = $db->loadObjectList();

        $this->options['0'] = n2_('All');

        if (count($menuItems)) {
            foreach ($menuItems AS $item) {
                $this->options[$item->lgid] = $item->name;
            }
        }
    }

}
