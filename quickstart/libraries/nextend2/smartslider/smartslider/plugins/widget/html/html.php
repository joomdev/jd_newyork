<?php
class N2SSPluginWidgetHTML extends N2SSPluginSliderWidget {

    public $ordering = 10;

    protected $name = 'html';

    public function getLabel() {
        return 'HTML';
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'widgetshtml');

        $url = N2Base::getApplication('smartslider')
                     ->getApplicationType('backend')->router->createAjaxUrl(array("slider/renderwidgethtml"));

        new N2ElementWidgetPluginMatrix($settings, 'widgethtml', false, '', $url, array(
            'widget' => $this
        ));


        $display = new N2ElementGroup($settings, 'widget-html-display', n2_('Display'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($display, 'widget-html-display-desktop', n2_('Desktop'), 1);
        new N2ElementOnOff($display, 'widget-html-display-tablet', n2_('Tablet'), 1);
        new N2ElementOnOff($display, 'widget-html-display-mobile', n2_('Mobile'), 1);
        new N2ElementText($display, 'widget-html-exclude-slides', n2_('Hide on slides'), '', array(
            'tip' => n2_('List the slides separated by commas on which you want the controls to be hidden.')
        ));

        new N2ElementOnOff($settings, 'widget-html-display-hover', n2_('Shows on hover'), 0);


        new N2TabPlaceholder($form, 'widget-html-placeholder', false, array(
            'id' => 'nextend-widgethtml-panel'
        ));

    }
}

N2SmartSliderWidgets::addGroup(new N2SSPluginWidgetHTML);
