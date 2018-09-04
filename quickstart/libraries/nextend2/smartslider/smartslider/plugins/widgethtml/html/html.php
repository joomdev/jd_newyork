<?php
N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');

class N2SSPluginWidgetHTMLHTML extends N2SSPluginWidgetAbstract {

    protected $name = 'html';

    private static $key = 'widget-html-';

    public function getDefaults() {
        return array(
            'widget-html-position-mode' => 'simple',
            'widget-html-position-area' => 2,
            'widget-html-code'          => '',
        );
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'widget-html');

        new N2ElementWidgetPosition($settings, 'widget-html-position', n2_('Position'));

        new N2ElementTextarea($settings, 'widget-html-code', 'HTML', '', array(
            'fieldStyle' => 'width: 600px; height: 600px;resize: vertical;'
        ));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR;
    }

    public function getPositions(&$params) {
        $positions                  = array();
        $positions['html-position'] = array(
            self::$key . 'position-',
            'html'
        );

        return $positions;
    }

    public function render($slider, $id, $params) {

        list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

        list($style, $attributes) = self::getPosition($params, self::$key);

        return N2Html::tag('div', $displayAttributes + $attributes + array(
                "class" => "n2-widget-html n2-notow {$displayClass}",
                "style" => "{$style}z-index: 10",
            ), $params->get(self::$key . 'code'));

    }
}

N2SmartSliderWidgets::addWidget('html', new N2SSPluginWidgetHTMLHTML);
