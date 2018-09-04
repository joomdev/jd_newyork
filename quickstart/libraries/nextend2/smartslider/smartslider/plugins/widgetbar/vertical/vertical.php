<?php
N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSPluginWidgetBarVertical extends N2SSPluginWidgetAbstract {

    private static $key = 'widget-bar-';

    protected $name = 'vertical';

    public function getDefaults() {
        return array(
            'widget-bar-position-mode'    => 'simple',
            'widget-bar-position-area'    => 6,
            'widget-bar-position-offset'  => 0,
            'widget-bar-style'            => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwYWIiLCJwYWRkaW5nIjoiMjB8KnwyMHwqfDIwfCp8MjB8KnxweCIsImJveHNoYWRvdyI6IjB8KnwwfCp8MHwqfDB8KnwwMDAwMDBmZiIsImJvcmRlciI6IjB8Knxzb2xpZHwqfDAwMDAwMGZmIiwiYm9yZGVycmFkaXVzIjoiMCIsImV4dHJhIjoiIn1dfQ==',
            'widget-bar-font-title'       => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiJmZmZmZmZmZiIsInNpemUiOiIxNnx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwYzciLCJhZm9udCI6Ik1vbnRzZXJyYXQiLCJsaW5laGVpZ2h0IjoiMS4zIiwiYm9sZCI6MCwiaXRhbGljIjowLCJ1bmRlcmxpbmUiOjAsImFsaWduIjoibGVmdCJ9LHsiY29sb3IiOiJmYzI4MjhmZiIsImFmb250IjoiZ29vZ2xlKEBpbXBvcnQgdXJsKGh0dHA6Ly9mb250cy5nb29nbGVhcGlzLmNvbS9jc3M/ZmFtaWx5PVJhbGV3YXkpOyksQXJpYWwiLCJzaXplIjoiMjV8fHB4In0se31dfQ==',
            'widget-bar-font-description' => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiJmZmZmZmZmZiIsInNpemUiOiIxMnx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwYzciLCJhZm9udCI6Ik1vbnRzZXJyYXQiLCJsaW5laGVpZ2h0IjoiMS42IiwiYm9sZCI6MCwiaXRhbGljIjowLCJ1bmRlcmxpbmUiOjAsImFsaWduIjoibGVmdCIsImV4dHJhIjoibWFyZ2luLXRvcDoxMHB4OyJ9LHsiY29sb3IiOiJmYzI4MjhmZiIsImFmb250IjoiZ29vZ2xlKEBpbXBvcnQgdXJsKGh0dHA6Ly9mb250cy5nb29nbGVhcGlzLmNvbS9jc3M/ZmFtaWx5PVJhbGV3YXkpOyksQXJpYWwiLCJzaXplIjoiMjV8fHB4In0se31dfQ==',
            'widget-bar-width'            => '200px',
            'widget-bar-height'           => '100%',
            'widget-bar-overlay'          => 0,
            'widget-bar-animate'          => 0
        );
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'bar-vertical');

        new N2ElementWidgetPosition($settings, 'widget-bar-position', n2_('Position'));

        new N2ElementOnOff($settings, 'widget-bar-animate', n2_('Animate'));

        new N2ElementStyle($settings, 'widget-bar-style', n2_('Style'), '', array(
            'previewMode' => 'simple',
            'font'        => 'sliderwidget-bar-font-title',
            'font2'       => 'sliderwidget-bar-font-description',
            'set'         => 1900,
            'preview'     => '<div style="width:300px;" class="{styleClassName}"><div class="{fontClassName}">Slide title</div><div class="{fontClassName2}">Slide description which is longer than the title</div></div>'
        ));

        new N2ElementFont($settings, 'widget-bar-font-title', n2_('Title font'), '', array(
            'previewMode' => 'simple',
            'style'       => 'sliderwidget-bar-style',
            'set'         => 1000,
            'preview'     => '<div style="width:300px;" class="{styleClassName}"><span class="{fontClassName}">Slide title</span></div>'
        ));

        new N2ElementFont($settings, 'widget-bar-font-description', n2_('Description font'), '', array(
            'previewMode' => 'simple',
            'style'       => 'sliderwidget-bar-style',
            'set'         => 1000,
            'preview'     => '<div style="width:300px;" class="{styleClassName}"><span class="{fontClassName}">Slide description which is longer than the title</span></div>'
        ));

        $size = new N2ElementGroup($settings, 'bar-vertical-size', n2_('Size'));
        new N2ElementText($size, 'widget-bar-width', n2_('Width'));
        new N2ElementText($size, 'widget-bar-height', n2_('Height'), '', array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($size, 'widget-bar-overlay', n2_('Overlay'), '', array(
            'rowClass' => 'n2-expert'
        ));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vertical' . DIRECTORY_SEPARATOR;
    }

    public function getPositions(&$params) {
        $positions = array();

        $positions['bar-position'] = array(
            self::$key . 'position-',
            'bar'
        );

        return $positions;
    }

    /**
     * @param $slider N2SmartSliderAbstract
     * @param $id
     * @param $params
     *
     * @return string
     */
    public function render($slider, $id, $params) {

        $slider->addLess(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vertical' . DIRECTORY_SEPARATOR . 'style.n2less'), array(
            "sliderid" => $slider->elementId
        ));
        $slider->features->addInitCallback(N2Filesystem::readFile(N2Filesystem::translate(dirname(__FILE__) . '/vertical/bar.min.js')));
    

        list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

        $styleClass = $slider->addStyle($params->get(self::$key . 'style'), 'simple');

        $fontTitle       = $slider->addFont($params->get(self::$key . 'font-title'), 'simple');
        $fontDescription = $slider->addFont($params->get(self::$key . 'font-description'), 'simple');

        list($style, $attributes) = self::getPosition($params, self::$key);
        $attributes['data-offset'] = $params->get(self::$key . 'position-offset');

        $attributes2 = array();

        $style  .= 'text-align: ' . $params->get(self::$key . 'align', 'left') . ';';
        $style2 = '';

        $width = $params->get(self::$key . 'width');
        if (is_numeric($width) || substr($width, -1) == '%' || substr($width, -2) == 'px') {
            $style .= 'width:' . $width . ';';
        } else {
            $attributes['data-sswidth'] = $width;
        }

        $height = $params->get(self::$key . 'height');
        if (is_numeric($height) || substr($height, -1) == '%' || substr($height, -2) == 'px') {
            $style2 .= 'height:' . $height . ';';
        } else {
            $attributes2['data-ssheight'] = $height;
        }

        $parameters = array(
            'overlay'         => ($params->get(self::$key . 'position-mode') != 'simple' || $params->get(self::$key . 'overlay') || substr($width, -2) != 'px') ? 1 : 0,
            'area'            => intval($params->get(self::$key . 'position-area')),
            'animate'         => intval($params->get(self::$key . 'animate')),
            'fontTitle'       => $fontTitle,
            'fontDescription' => $fontDescription
        );

        $slider->exposeSlideData['title']       = true;
        $slider->exposeSlideData['description'] = true;

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetBarVertical(this, ' . json_encode($parameters) . ');');

        return N2Html::tag("div", $displayAttributes + $attributes + $attributes2 + array(
                "class" => $displayClass . "nextend-bar nextend-bar-vertical n2-ow",
                "style" => $style . $style2
            ), N2Html::tag("div", $attributes2 + array(
                "class" => $styleClass . ' n2-ow',
                "style" => $style2
            ), N2Html::tag("div", array('class' => 'n2-ow'), '')));
    }

    public function prepareExport($export, $params) {
        $export->addVisual($params->get(self::$key . 'style'));
        $export->addVisual($params->get(self::$key . 'font-title'));
        $export->addVisual($params->get(self::$key . 'font-description'));
    }

    public function prepareImport($import, $params) {

        $params->set(self::$key . 'style', $import->fixSection($params->get(self::$key . 'style', '')));
        $params->set(self::$key . 'font-title', $import->fixSection($params->get(self::$key . 'font-title', '')));
        $params->set(self::$key . 'font-description', $import->fixSection($params->get(self::$key . 'font-description', '')));
    }

}

N2SmartSliderWidgets::addWidget('bar', new N2SSPluginWidgetBarVertical);
