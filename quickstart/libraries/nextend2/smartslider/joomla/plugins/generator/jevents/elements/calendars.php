<?php

N2Loader::import('libraries.form.element.list');

class N2ElementJEventsCalendars extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model     = new N2Model('jevents_icsfile');
        $query     = "SELECT ics_id, label FROM #__jevents_icsfile WHERE state = '1'";
        $calendars = $model->db->queryAll($query, false, "object");

        $this->options['0'] = n2_('All');

        if (count($calendars)) {
            foreach ($calendars AS $calendar) {
                $this->options[$calendar->ics_id] = $calendar->label;
            }
        }

    }

}
