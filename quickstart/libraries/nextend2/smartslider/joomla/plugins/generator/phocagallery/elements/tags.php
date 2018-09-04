<?php

N2Loader::import('libraries.form.element.list');

class N2ElementPhocaGalleryTags extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $query = 'SELECT id, title FROM #__phocagallery_tags WHERE published = 1 ORDER BY ordering';

        $model = new N2Model('phocagallery_tags');
        $tags  = $model->db->queryAll($query, false, "object");

        $this->options['0'] = n2_('All');

        if (count($tags)) {
            foreach ($tags AS $tag) {
                $this->options[$tag->id] = $tag->title;
            }
        }
    }

}
