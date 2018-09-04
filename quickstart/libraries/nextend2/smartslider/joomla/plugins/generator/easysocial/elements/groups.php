<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEasySocialGroups extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model      = new N2Model('social_clusters_categories');
        $categories = $model->db->queryAll("SELECT id, title FROM #__social_clusters WHERE state = 1 AND cluster_type='group' ORDER BY id", false, "object");

        $this->options['0'] = n2_('All');

        if (count($categories)) {
            foreach ($categories AS $category) {
                $this->options[$category->id] = $category->title;
            }
        }
    }
}