<?php

class N2SSPluginWidgetBar extends N2SSPluginSliderWidget {

    public $ordering = 5;

    protected $name = 'bar';

    public function getLabel() {
        return n2_('Text Bar');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'widgetsbar');

        $url = N2Base::getApplication('smartslider')
                     ->getApplicationType('backend')->router->createAjaxUrl(array("slider/renderwidgetbar"));

        new N2ElementWidgetPluginMatrix($settings, 'widgetbar', false, '', $url, array(
            'widget' => $this
        ));
        $display = new N2ElementGroup($settings, 'widget-bar-display', n2_('Display'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($display, 'widget-bar-display-desktop', n2_('Desktop'), 1);
        new N2ElementOnOff($display, 'widget-bar-display-tablet', n2_('Tablet'), 1);
        new N2ElementOnOff($display, 'widget-bar-display-mobile', n2_('Mobile'), 1);
        new N2ElementText($display, 'widget-bar-exclude-slides', n2_('Hide on slides'), '', array(
            'tip' => n2_('List the slides separated by commas on which you want the controls to be hidden.')
        ));

    

        new N2ElementOnOff($settings, 'widget-bar-display-hover', n2_('Shows on hover'), 0);


        new N2TabPlaceholder($form, 'widget-bar-placeholder', false, array(
            'id' => 'nextend-widgetbar-panel'
        ));

    }
}

N2SmartSliderWidgets::addGroup(new N2SSPluginWidgetBar);