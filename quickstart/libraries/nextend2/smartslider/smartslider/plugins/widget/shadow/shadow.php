<?php

class N2SSPluginWidgetShadow extends N2SSPluginSliderWidget {

    public $ordering = 7;

    protected $name = 'shadow';

    public function getLabel() {
        return n2_('Shadows');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'widgetsshadow');

        $url = N2Base::getApplication('smartslider')
                     ->getApplicationType('backend')->router->createAjaxUrl(array("slider/renderwidgetshadow"));

        new N2ElementWidgetPluginMatrix($settings, 'widgetshadow', false, '', $url, array(
            'widget' => $this
        ));
        $display = new N2ElementGroup($settings, 'widget-shadow-display', n2_('Display'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($display, 'widget-shadow-display-desktop', n2_('Desktop'), 1);
        new N2ElementOnOff($display, 'widget-shadow-display-tablet', n2_('Tablet'), 1);
        new N2ElementOnOff($display, 'widget-shadow-display-mobile', n2_('Mobile'), 1);
        new N2ElementText($display, 'widget-shadow-exclude-slides', n2_('Hide on slides'), '', array(
            'tip' => n2_('List the slides separated by commas on which you want the controls to be hidden.')
        ));

    

        new N2TabPlaceholder($form, 'widget-shadow-placeholder', false, array(
            'id' => 'nextend-widgetshadow-panel'
        ));

    }
}

N2SmartSliderWidgets::addGroup(new N2SSPluginWidgetShadow);