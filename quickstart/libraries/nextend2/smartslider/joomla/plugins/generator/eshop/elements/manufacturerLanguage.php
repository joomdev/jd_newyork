<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEShopManufacturerLanguage extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model("eshop_manufacturerdetails");

        $query = 'SELECT language
                  FROM #__eshop_manufacturerdetails
                  GROUP BY language';

        $languages = $model->db->queryAll($query, false, "object");

        $this->options[0] = n2_('Default');
        if (count($languages)) {
            foreach ($languages AS $language) {
                $this->options[$language->language] = $language->language;
            }
        }
    }

}
