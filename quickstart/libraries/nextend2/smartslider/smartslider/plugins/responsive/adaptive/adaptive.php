<?php
class N2SSPluginResponsiveAdaptive extends N2SSPluginSliderResponsive {

    protected $name = 'adaptive';

    public $ordering = 4;

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
    }

    public function getLabel() {
        return n2_x('Fixed', 'Slider responsive mode');
    }

    public function renderFields($form) {
    }
}

N2SSPluginSliderResponsive::addType(new N2SSPluginResponsiveAdaptive);
