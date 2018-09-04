<?php

N2Loader::import('libraries.form.element.list');

class N2ElementMijoShopLanguages extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model('mijoshop_language');

        $query = 'SELECT lang_id, title
                FROM #__languages
                WHERE published = 1';

        $languages = $model->db->queryAll($query, false, "object");

        $this->options['0'] = 'Auto';

        if (count($languages)) {
            foreach ($languages AS $language) {
                $this->options[$language->lang_id] = $language->title;
            }
        }
    }

}
