<?php

class N2SSPluginWidgetAutoplay extends N2SSPluginSliderWidget {

    public $ordering = 3;

    protected $name = 'autoplay';

    public function getLabel() {
        return n2_('Autoplay');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'widgetsautoplay');

        $url = N2Base::getApplication('smartslider')
                     ->getApplicationType('backend')->router->createAjaxUrl(array("slider/renderwidgetautoplay"));

        new N2ElementWidgetPluginMatrix($settings, 'widgetautoplay', false, 'disabled', $url, array(
            'widget' => $this
        ));
        $display = new N2ElementGroup($settings, 'widget-autoplay-display', n2_('Display'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($display, 'widget-autoplay-display-desktop', n2_('Desktop'), 1);
        new N2ElementOnOff($display, 'widget-autoplay-display-tablet', n2_('Tablet'), 1);
        new N2ElementOnOff($display, 'widget-autoplay-display-mobile', n2_('Mobile'), 1);
        new N2ElementText($display, 'widget-autoplay-exclude-slides', n2_('Hide on slides'), '', array(
            'tip' => n2_('List the slides separated by commas on which you want the controls to be hidden.')
        ));

    

        new N2ElementOnOff($settings, 'widget-autoplay-display-hover', n2_('Shows on hover'), 0);


        new N2TabPlaceholder($form, 'widget-autoplay-placeholder', false, array(
            'id' => 'nextend-widgetautoplay-panel'
        ));
    }
}

N2SmartSliderWidgets::addGroup(new N2SSPluginWidgetAutoplay);