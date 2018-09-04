<?php

N2Loader::import('libraries.form.element.list');

class N2ElementMijoShopManufacturers extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model("mijoshop_manufacturer");

        $query = 'SELECT manufacturer_id AS id, name FROM #__mijoshop_manufacturer ORDER BY sort_order, id';

        $manufacturers = $model->db->queryAll($query, false, "object");

        $this->options['0'] = n2_('All');

        if (count($manufacturers)) {
            foreach ($manufacturers AS $manufacturer) {
                $this->options[$manufacturer->id] = $manufacturer->name;
            }
        }
    }

}
