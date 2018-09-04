<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEShopCurrency extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model("eshop_currencies");

        $query = 'SELECT currency_code
                  FROM #__eshop_currencies
                  ORDER BY id';

        $codes = $model->db->queryAll($query, false, "object");

        $this->options[0] = n2_('Default');
        if (count($codes)) {
            foreach ($codes AS $code) {
                $this->options[$code->currency_code] = $code->currency_code;
            }
        }
    }

}
