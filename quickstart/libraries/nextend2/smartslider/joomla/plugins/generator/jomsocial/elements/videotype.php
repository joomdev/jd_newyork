<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJomSocialVideoType extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model('community_videos');

        $query = "SELECT type FROM #__community_videos GROUP BY type";
        $types = $model->db->queryAll($query, false, "object");

        $this->options['0']       = n2_('All');
        $this->options['youtube'] = 'YouTube';

        if (count($types)) {
            foreach ($types AS $type) {
                if ($type->type != 'youtube') {
                    $this->options[$type->type] = ucfirst($type->type);
                }
            }
        }

    }
}