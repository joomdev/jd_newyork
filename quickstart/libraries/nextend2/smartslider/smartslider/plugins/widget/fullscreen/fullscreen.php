<?php
class N2SSPluginWidgetFullScreen extends N2SSPluginSliderWidget {

    public $ordering = 9;

    protected $name = 'fullscreen';

    public function getLabel() {
        return n2_('Full screen');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'widgetsfullscreen');

        $url = N2Base::getApplication('smartslider')
                     ->getApplicationType('backend')->router->createAjaxUrl(array("slider/renderwidgetfullscreen"));

        new N2ElementWidgetPluginMatrix($settings, 'widgetfullscreen', false, '', $url, array(
            'widget' => $this
        ));

        $display = new N2ElementGroup($settings, 'widget-fullscreen-display', n2_('Display'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($display, 'widget-fullscreen-display-desktop', n2_('Desktop'), 1);
        new N2ElementOnOff($display, 'widget-fullscreen-display-tablet', n2_('Tablet'), 1);
        new N2ElementOnOff($display, 'widget-fullscreen-display-mobile', n2_('Mobile'), 1);
        new N2ElementText($display, 'widget-fullscreen-exclude-slides', n2_('Hide on slides'), '', array(
            'tip' => n2_('List the slides separated by commas on which you want the controls to be hidden.')
        ));


        new N2ElementOnOff($settings, 'widget-fullscreen-display-hover', n2_('Shows on hover'), 0);


        new N2TabPlaceholder($form, 'widget-fullscreen-placeholder', false, array(
            'id' => 'nextend-widgetfullscreen-panel'
        ));

    }
}

N2SmartSliderWidgets::addGroup(new N2SSPluginWidgetFullScreen);
