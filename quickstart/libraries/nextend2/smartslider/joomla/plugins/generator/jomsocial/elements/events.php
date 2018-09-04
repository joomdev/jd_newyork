<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJomSocialEvents extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);


        $model = new N2Model('community_events');

        $query  = "SELECT id, title FROM #__community_events ORDER BY id";
        $events = $model->db->queryAll($query, false, "object");

        $this->options['0'] = n2_('All');
        if (count($events)) {
            foreach ($events AS $event) {
                $this->options[$event->id] = $event->title;
            }
        }

    }
}