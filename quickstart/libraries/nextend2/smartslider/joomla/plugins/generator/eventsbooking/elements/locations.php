<?php

N2Loader::import('libraries.form.element.list');

class N2ElementEventsBookingLocations extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $model = new N2Model("events_booking_locations");
        $query     = "SELECT id, name FROM #__eb_locations";
        $locations = $model->db->queryAll($query, false, "object");

        $this->options[0] = n2_('All');

        if (count($locations)) {
            foreach ($locations AS $location) {
                $this->options[$location->id] = $location->name;
            }
        }
    }

}
