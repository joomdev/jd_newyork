<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEasyDiscussTags extends N2ElementList {

    protected $isMultiple = true;
    protected $size = 10;

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model('discuss_tags');

        $menuItems = $model->db->queryAll('SELECT * FROM #__discuss_tags WHERE published = 1 ORDER BY id', false, "object");

        $this->options['0'] = n2_('All');

        if (count($menuItems)) {
            foreach ($menuItems AS $option) {
                $this->options[$option->id] = $option->title;
            }
        }
    }
}