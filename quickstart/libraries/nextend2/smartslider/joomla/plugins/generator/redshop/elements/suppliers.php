<?php

N2Loader::import('libraries.form.element.list');

class N2ElementRedShopSuppliers extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model('redshop_supplier');

        $query = 'SELECT name, id FROM #__redshop_supplier WHERE published = 1 ORDER BY id';

        $suppliers = $model->db->queryAll($query, false, "object");

        $this->options['0'] = n2_('All');

        if (count($suppliers)) {
            foreach ($suppliers AS $supplier) {
                $this->options[$supplier->id] = $supplier->name;
            }
        }
    }

}
