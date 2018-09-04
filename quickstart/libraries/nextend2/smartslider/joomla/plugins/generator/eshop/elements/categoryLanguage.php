<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEShopCategoryLanguage extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model("eshop_categorydetails");

        $query = 'SELECT language
                  FROM #__eshop_categorydetails
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
