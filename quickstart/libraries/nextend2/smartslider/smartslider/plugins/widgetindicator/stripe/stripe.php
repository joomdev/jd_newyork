<?php
N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSPluginWidgetIndicatorStripe extends N2SSPluginWidgetAbstract {

    protected $name = 'stripe';

    private static $key = 'widget-indicator-';

    public function getDefaults() {
        return array(
            'widget-indicator-position-mode' => 'simple',
            'widget-indicator-position-area' => 9,
            'widget-indicator-width'         => '100%',
            'widget-indicator-height'        => 6,
            'widget-indicator-overlay'       => 0,
            'widget-indicator-track'         => '000000ab',
            'widget-indicator-bar'           => '00c1c4ff'
        );
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'widget-indicator-stripe');

        new N2ElementWidgetPosition($settings, 'widget-indicator-position', n2_('Position'));

        $size = new N2ElementGroup($settings, 'indicator-size', n2_('Size'));
        new N2ElementText($size, 'widget-indicator-width', n2_('Width'), '', array(
            'style'    => 'width:30px;',
            'unit'     => 'px',
            'rowClass' => 'n2-expert'
        ));
        new N2ElementNumber($size, 'widget-indicator-height', n2_('Height'), '', array(
            'style' => 'width:30px;',
            'unit'  => 'px'
        ));
        new N2ElementOnOff($size, 'widget-indicator-overlay', n2_('Overlay'));


        $color = new N2ElementGroup($settings, 'indicator-color', n2_('Color'));
        new N2ElementColor($color, 'widget-indicator-track', n2_('Track'), '', array(
            'alpha' => true
        ));
        new N2ElementColor($color, 'widget-indicator-bar', n2_('Bar'), '', array(
            'alpha' => true
        ));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'stripe' . DIRECTORY_SEPARATOR;
    }

    public function getPositions(&$params) {
        $positions                       = array();
        $positions['indicator-position'] = array(
            self::$key . 'position-',
            'indicator'
        );

        return $positions;
    }

    public function render($slider, $id, $params) {

        $slider->addLess(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'stripe' . DIRECTORY_SEPARATOR . 'style.n2less'), array(
            "sliderid" => $slider->elementId
        ));
        $slider->features->addInitCallback(N2Filesystem::readFile(N2Filesystem::translate(dirname(__FILE__) . '/stripe/indicator.min.js')));
    

        list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

        list($trackHex, $trackRGBA) = N2Color::colorToCss($params->get(self::$key . 'track'));
        list($barHex, $barRGBA) = N2Color::colorToCss($params->get(self::$key . 'bar'));

        list($style, $attributes) = self::getPosition($params, self::$key);
        $attributes['data-offset'] = $params->get(self::$key . 'position-offset', 0);


        $width = $params->get(self::$key . 'width');
        if (is_numeric($width) || substr($width, -1) == '%' || substr($width, -2) == 'px') {
            $style .= 'width:' . $width . ';';
        } else {
            $attributes['data-sswidth'] = $width;
        }

        $height = intval($params->get(self::$key . 'height'));

        $parameters = array(
            'overlay' => $params->get(self::$key . 'position-mode') != 'simple' || $params->get(self::$key . 'overlay'),
            'area'    => intval($params->get(self::$key . 'position-area'))
        );

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetIndicatorStripe(this, ' . json_encode($parameters) . ');');

        return N2Html::tag('div', $displayAttributes + $attributes + array(
                'class' => $displayClass . "nextend-indicator nextend-indicator-stripe n2-ow",
                'style' => 'background-color:#' . $trackHex . ';background-color:' . $trackRGBA . ';' . $style
            ), N2Html::tag('div', array(
            'class' => "nextend-indicator-track  n2-ow",
            'style' => 'height: ' . $height . 'px;background-color:#' . $barHex . ';background-color:' . $barRGBA . ';'
        ), ''));
    }
}

N2SmartSliderWidgets::addWidget('indicator', new N2SSPluginWidgetIndicatorStripe);
