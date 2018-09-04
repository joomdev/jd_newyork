<?php

N2Loader::import('libraries.form.element.list');

class N2ElementRedShopManufacturers extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model('redshop_manufacturer');

        $query = 'SELECT manufacturer_name, manufacturer_id FROM #__redshop_manufacturer WHERE published = 1 ORDER BY ordering';

        $manufacturers = $model->db->queryAll($query, false, "object");

        $this->options['0'] = n2_('All');

        if (count($manufacturers)) {
            foreach ($manufacturers AS $manufacturer) {
                $this->options[$manufacturer->manufacturer_id] = $manufacturer->manufacturer_name;
            }
        }
    }

}
