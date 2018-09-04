<?php
N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSPluginWidgetIndicatorPie extends N2SSPluginWidgetAbstract {

    protected $name = 'pie';

    private static $key = 'widget-indicator-';

    public function getDefaults() {
        return array(
            'widget-indicator-position-mode'   => 'simple',
            'widget-indicator-position-area'   => 4,
            'widget-indicator-position-offset' => 15,
            'widget-indicator-size'            => 25,
            'widget-indicator-thickness'       => 30,
            'widget-indicator-track'           => '000000ab',
            'widget-indicator-bar'             => 'ffffffff'
        );
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'widget-indicator-pie');

        new N2ElementWidgetPosition($settings, 'widget-indicator-position', n2_('Position'));

        $size = new N2ElementGroup($settings, 'indicator-size', n2_('Size'));
        new N2ElementNumber($size, 'widget-indicator-size', n2_('Size'), '', array(
            'style' => 'width:30px;',
            'unit'  => 'px'
        ));
        new N2ElementNumber($size, 'widget-indicator-thickness', n2_('Line thickness'), '', array(
            'style' => 'width:30px;',
            'unit'  => '%'
        ));

        $color = new N2ElementGroup($settings, 'indicator-color', n2_('Color'));
        new N2ElementColor($color, 'widget-indicator-track', n2_('Track'), '', array(
            'alpha' => true
        ));
        new N2ElementColor($color, 'widget-indicator-bar', n2_('Bar'), '', array(
            'alpha' => true
        ));

        new N2ElementStyle($settings, 'widget-indicator-style', n2_('Style'), '', array(
            'previewMode' => 'button',
            'set'         => 1900,
            'preview'     => '<div class="{styleClassName}" style="display: inline-block;"><svg style="display:block; height="{parseInt($(\'#sliderwidget-indicator-size\').val())+4}" width="{parseInt($(\'#sliderwidget-indicator-size\').val())+4}"><circle cx="{(parseInt($(\'#sliderwidget-indicator-size\').val())+4)/2}" cy="{(parseInt($(\'#sliderwidget-indicator-size\').val())+4)/2}" r="{((2-parseInt($(\'#sliderwidget-indicator-thickness\').val())/100)*parseInt($(\'#sliderwidget-indicator-size\').val()))/4}" stroke-width="{parseInt($(\'#sliderwidget-indicator-size\').val())/2*parseInt($(\'#sliderwidget-indicator-thickness\').val())/100}" stroke="#{$(\'#sliderwidget-indicator-bar\').val().substr(0, 6)}" stroke-opacity="{N2Color.hex2alpha($(\'#sliderwidget-indicator-bar\').val())}" fill="none"></circle></svg></div>'
        ));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'pie' . DIRECTORY_SEPARATOR;
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
        $slider->features->addInitCallback(N2Filesystem::readFile(N2Filesystem::translate(dirname(__FILE__) . '/pie/indicator.min.js')));
    

        list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);


        $isNormalFlow = self::isNormalFlow($params, self::$key);

        list($style, $attributes) = self::getPosition($params, self::$key);

        $track      = N2Color::colorToSVG($params->get(self::$key . 'track'));
        $bar        = N2Color::colorToSVG($params->get(self::$key . 'bar'));
        $parameters = array(
            'backstroke'         => $track[0],
            'backstrokeopacity'  => $track[1],
            'frontstroke'        => $bar[0],
            'frontstrokeopacity' => $bar[1],
            'size'               => intval($params->get(self::$key . 'size')),
            'thickness'          => $params->get(self::$key . 'thickness') / 100
        );

        $styleClass = $slider->addStyle($params->get(self::$key . 'style'), 'heading');

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetIndicatorPie(this, ' . json_encode($parameters) . ');');

        return N2Html::tag('div', $displayAttributes + $attributes + array(
                'class' => $displayClass . $styleClass . " nextend-indicator nextend-indicator-pie n2-ow" . ($isNormalFlow ? ' n2-flex' : ' n2-ib'),
                'style' => $style . ($isNormalFlow ? 'justify-content:center;' : '')
            ));
    }
}

N2SmartSliderWidgets::addWidget('indicator', new N2SSPluginWidgetIndicatorPie);

class N2SSPluginWidgetIndicatorPieFull extends N2SSPluginWidgetIndicatorPie {

    protected $name = 'pieFull';

    public function getDefaults() {
        return array_merge(parent::getDefaults(), array(
            'widget-indicator-thickness' => 100,
            'widget-indicator-track'     => 'ffffff00',
            'widget-indicator-bar'       => 'ffffff80',
        ));
    }
}

N2SmartSliderWidgets::addWidget('indicator', new N2SSPluginWidgetIndicatorPieFull);
