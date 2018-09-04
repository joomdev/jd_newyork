<?php

N2Loader::import('libraries.form.element.list');

class N2ElementVirtueMartManufacturers extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model('virtuemart_manufacturers');
        require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_virtuemart' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'config.php');
        VmConfig::loadConfig();
        $query = 'SELECT virtuemart_manufacturer_id AS id, mf_name AS name FROM #__virtuemart_manufacturers_' . VMLANG . ' ORDER BY id';

        $manufacturers = $model->db->queryAll($query, false, "object");

        $this->options['0'] = n2_('All');

        if (count($manufacturers)) {
            foreach ($manufacturers AS $manufacturer) {
                $this->options[$manufacturer->id] = $manufacturer->name;
            }
        }

    }

}
