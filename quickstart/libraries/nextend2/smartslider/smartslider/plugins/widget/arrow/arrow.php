<?php

class N2SSPluginWidgetArrow extends N2SSPluginSliderWidget {

    protected $name = 'arrow';

    public function getLabel() {
        return n2_('Arrows');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'widgetsarrow');

        $url = N2Base::getApplication('smartslider')
                     ->getApplicationType('backend')->router->createAjaxUrl(array("slider/renderwidgetarrow"));

        new N2ElementWidgetPluginMatrix($settings, 'widgetarrow', false, 'imageEmpty', $url, array(
            'widget' => $this
        ));
        $display = new N2ElementGroup($settings, 'widget-arrow-display', n2_('Display'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($display, 'widget-arrow-display-desktop', n2_('Desktop'), 1);
        new N2ElementOnOff($display, 'widget-arrow-display-tablet', n2_('Tablet'), 1);
        new N2ElementOnOff($display, 'widget-arrow-display-mobile', n2_('Mobile'), 1);
        new N2ElementText($display, 'widget-arrow-exclude-slides', n2_('Hide on slides'), '', array(
            'tip' => n2_('List the slides separated by commas on which you want the controls to be hidden.')
        ));

    

        new N2ElementOnOff($settings, 'widget-arrow-display-hover', n2_('Shows on hover'), 0);


        new N2TabPlaceholder($form, 'widget-arrow-placeholder', false, array(
            'id' => 'nextend-widgetarrow-panel'
        ));
    }
}

N2SmartSliderWidgets::addGroup(new N2SSPluginWidgetArrow);