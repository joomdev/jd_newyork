<?php
class N2SSPluginWidgetIndicator extends N2SSPluginSliderWidget {

    public $ordering = 4;

    protected $name = 'indicator';

    public function getLabel() {
        return n2_('Indicator');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'widgetsindicator');

        $url = N2Base::getApplication('smartslider')
                     ->getApplicationType('backend')->router->createAjaxUrl(array("slider/renderwidgetindicator"));

        new N2ElementWidgetPluginMatrix($settings, 'widgetindicator', false, '', $url, array(
            'widget' => $this
        ));

        $display = new N2ElementGroup($settings, 'widget-indicator-display', n2_('Display'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($display, 'widget-indicator-display-desktop', n2_('Desktop'), 1);
        new N2ElementOnOff($display, 'widget-indicator-display-tablet', n2_('Tablet'), 1);
        new N2ElementOnOff($display, 'widget-indicator-display-mobile', n2_('Mobile'), 1);
        new N2ElementText($display, 'widget-indicator-exclude-slides', n2_('Hide on slides'), '', array(
            'tip' => n2_('List the slides separated by commas on which you want the controls to be hidden.')
        ));

        new N2ElementOnOff($settings, 'widget-indicator-display-hover', n2_('Shows on hover'), 0);


        new N2TabPlaceholder($form, 'widget-indicator-placeholder', false, array(
            'id' => 'nextend-widgetindicator-panel'
        ));

    }
}

N2SmartSliderWidgets::addGroup(new N2SSPluginWidgetIndicator);
