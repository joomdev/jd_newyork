<?php

N2Loader::import('libraries.form.element.list');

class N2ElementHikaShopWarehouses extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);
        $model = new N2Model('tags');

        $query = "SELECT warehouse_name, warehouse_id FROM #__hikashop_warehouse WHERE warehouse_published = 1";

        $warehouses = $model->db->queryAll($query, false, "object");

        $this->options['0'] = n2_('All');

        if (count($warehouses)) {
            foreach ($warehouses AS $warehouse) {
                $this->options[$warehouse->warehouse_id] = $warehouse->warehouse_name;
            }
        }
    }

}
