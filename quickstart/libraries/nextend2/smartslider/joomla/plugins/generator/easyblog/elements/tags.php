<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEasyBlogTags extends N2ElementList {

    protected $isMultiple = true;
    protected $size = 10;

    public function __construct(N2FormElementContainer $parent, $name = '', $label = '', $default = '', array $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model('easyblog_tag');

        $menuItems = $model->db->queryAll('SELECT * FROM #__easyblog_tag WHERE published = 1 ORDER BY ordering, id', false, "object");

        $this->options['0'] = n2_('All');

        if (count($menuItems)) {
            foreach ($menuItems AS $option) {
                $this->options[$option->id] = $option->title;
            }
        }
    }
}