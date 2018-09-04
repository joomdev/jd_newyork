<?php
N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSPluginWidgetBulletNumbers extends N2SSPluginWidgetAbstract {

    protected $name = 'numbers';

    private static $key = 'widget-bullet-';

    public function getDefaults() {
        return array(
            'widget-bullet-position-mode'        => 'simple',
            'widget-bullet-position-area'        => 10,
            'widget-bullet-position-offset'      => 5,
            'widget-bullet-action'               => 'click',
            'widget-bullet-style'                => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwYWIiLCJwYWRkaW5nIjoiNXwqfDl8Knw1fCp8OXwqfHB4IiwiYm94c2hhZG93IjoiMHwqfDB8KnwwfCp8MHwqfDAwMDAwMGZmIiwiYm9yZGVyIjoiMHwqfHNvbGlkfCp8MDAwMDAwZmYiLCJib3JkZXJyYWRpdXMiOiIwIiwiZXh0cmEiOiJtaW4td2lkdGg6IDEwcHg7XG5tYXJnaW46IDRweDsifSx7ImJhY2tncm91bmRjb2xvciI6IjVjYmEzY2ZmIiwicGFkZGluZyI6IjV8Knw5fCp8NXwqfDl8KnxweCJ9XX0=',
            'widget-bullet-font'                 => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiJmZmZmZmZmZiIsInNpemUiOiIxNHx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJhZm9udCI6Ik1vbnRzZXJyYXQiLCJsaW5laGVpZ2h0IjoiMS4yIiwiYm9sZCI6MCwiaXRhbGljIjowLCJ1bmRlcmxpbmUiOjAsImFsaWduIjoiY2VudGVyIiwiZXh0cmEiOiIifSx7ImNvbG9yIjoiZmZmZmZmZmYifV19',
            'widget-bullet-bar'                  => '',
            'widget-bullet-align'                => 'center',
            'widget-bullet-orientation'          => 'auto',
            'widget-bullet-bar-full-size'        => 0,
            'widget-bullet-overlay'              => 0,
            'widget-bullet-thumbnail-show-image' => 0,
            'widget-bullet-thumbnail-width'      => 100,
            'widget-bullet-thumbnail-width'      => 60,
            'widget-bullet-thumbnail-style'      => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwODAiLCJwYWRkaW5nIjoiM3wqfDN8KnwzfCp8M3wqfHB4IiwiYm94c2hhZG93IjoiMHwqfDB8KnwwfCp8MHwqfDAwMDAwMGZmIiwiYm9yZGVyIjoiMHwqfHNvbGlkfCp8MDAwMDAwZmYiLCJib3JkZXJyYWRpdXMiOiIzIiwiZXh0cmEiOiJtYXJnaW46IDVweDsifV19',
            'widget-bullet-thumbnail-side'       => 'before'
        );
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'bullet-number');

        new N2ElementWidgetPosition($settings, 'widget-bullet-position', n2_('Position'));

        new N2ElementRadio($settings, 'widget-bullet-action', n2_('Action'), '', array(
            'rowClass' => 'n2-expert',
            'options'  => array(
                'click'      => n2_('Click'),
                'mouseenter' => n2_('Hover')
            )
        ));

        new N2ElementStyle($settings, 'widget-bullet-style', n2_('Dot style'), '', array(
            'previewMode' => 'dot',
            'font'        => 'sliderwidget-bullet-font',
            'style2'      => 'sliderwidget-bullet-bar',
            'set'         => 1900,
            'preview'     => '<div class="{styleClassName2}" style="display:inline-block;"><div class="{styleClassName} {fontClassName}" style="display: inline-block; vertical-align:top;">1</div><div class="{styleClassName} {fontClassName} n2-active" style="display: inline-block; vertical-align:top;">2</div><div class="{styleClassName} {fontClassName}" style="display: inline-block; vertical-align:top;">3</div><div class="{styleClassName} {fontClassName}" style="display: inline-block; vertical-align:top;">4</div></div>'
        ));

        new N2ElementFont($settings, 'widget-bullet-font', n2_('Font'), '', array(
            'previewMode' => 'dot',
            'style'       => 'sliderwidget-bullet-style',
            'style2'      => 'sliderwidget-bullet-bar',
            'set'         => 1100,
            'preview'     => '<div class="{styleClassName2}" style="display:inline-block;"><div class="{styleClassName} {fontClassName}" style="display: inline-block; vertical-align:top;">1</div><div class="{styleClassName} {fontClassName} n2-active" style="display: inline-block; vertical-align:top;">2</div><div class="{styleClassName} {fontClassName}" style="display: inline-block; vertical-align:top;">3</div><div class="{styleClassName} {fontClassName}" style="display: inline-block; vertical-align:top;">4</div></div>'
        ));

        new N2ElementStyle($settings, 'widget-bullet-bar', n2_('Bar style'), '', array(
            'previewMode' => 'simple',
            'font'        => 'sliderwidget-bullet-font',
            'style2'      => 'sliderwidget-bullet-style',
            'set'         => 1900,
            'preview'     => '<div class="{styleClassName}" style="display:inline-block;"><div class="{styleClassName2} {fontClassName}" style="display: inline-block; vertical-align:top;">1</div><div class="{styleClassName2} {fontClassName} n2-active" style="display: inline-block; vertical-align:top;">2</div><div class="{styleClassName2} {fontClassName}" style="display: inline-block; vertical-align:top;">3</div><div class="{styleClassName2} {fontClassName}" style="display: inline-block; vertical-align:top;">4</div></div>'
        ));

        new N2ElementOnOff($settings, 'widget-bullet-bar-full-size', n2_('Bar full size'), '', array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementRadio($settings, 'widget-bullet-align', n2_('Align'), '', array(
            'rowClass' => 'n2-expert',
            'options'  => array(
                'left'   => n2_('Left'),
                'center' => n2_('Center'),
                'right'  => n2_('Right')
            )
        ));
        new N2ElementRadio($settings, 'widget-bullet-orientation', n2_('Orientation'), '', array(
            'rowClass' => 'n2-expert',
            'options'  => array(
                'auto'       => n2_('Auto'),
                'horizontal' => n2_('Horizontal'),
                'vertical'   => n2_('Vertical')
            )
        ));
        new N2ElementOnOff($settings, 'widget-bullet-overlay', n2_('Overlay'), '', array(
            'rowClass' => 'n2-expert'
        ));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'numbers' . DIRECTORY_SEPARATOR;
    }

    public function getPositions(&$params) {
        $positions                    = array();
        $positions['bullet-position'] = array(
            self::$key . 'position-',
            'bullet'
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
        if (count($slider->slides) <= 1) {
            return '';
        }

        $slider->addLess(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'numbers' . DIRECTORY_SEPARATOR . 'style.n2less'), array(
            "sliderid" => $slider->elementId
        ));
        $slider->features->addInitCallback(N2Filesystem::readFile(N2Filesystem::translate(dirname(__FILE__) . '/../transition/transition/bullet.min.js')));
    

        list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

        $bulletStyle = $slider->addStyle($params->get(self::$key . 'style'), 'dot');
        $barStyle    = $slider->addStyle($params->get(self::$key . 'bar'), 'simple');

        $bulletFont = $slider->addFont($params->get(self::$key . 'font'), 'dot');

        list($style, $attributes) = self::getPosition($params, self::$key);
        $attributes['data-offset'] = $params->get(self::$key . 'position-offset', 0);

        $orientation = self::getOrientationByPosition($params->get(self::$key . 'position-mode'), $params->get(self::$key . 'position-area'), $params->get(self::$key . 'orientation'), 'horizontal');

        $parameters = array(
            'overlay'    => ($params->get(self::$key . 'position-mode') || $params->get(self::$key . 'overlay')) ? 1 : 0,
            'area'       => intval($params->get(self::$key . 'position-area')),
            'dotClasses' => $bulletStyle . $bulletFont,
            'mode'       => 'numeric',
            'action'     => $params->get(self::$key . 'action')
        );

        if ($params->get(self::$key . 'thumbnail-show-image')) {

            $slider->exposeSlideData['thumbnail'] = true;

            $parameters['thumbnail']       = 1;
            $parameters['thumbnailWidth']  = intval($params->get(self::$key . 'thumbnail-width'));
            $parameters['thumbnailHeight'] = intval($params->get(self::$key . 'thumbnail-height'));
            $parameters['thumbnailStyle']  = $slider->addStyle($params->get(self::$key . 'thumbnail-style'), 'simple', '');
            $side                          = $params->get(self::$key . 'thumbnail-side', 'before');


            if ($side == 'before') {
                if ($orientation == 'vertical') {
                    $position = 'left';
                } else {
                    $position = 'top';
                }
            } else {
                if ($orientation == 'vertical') {
                    $position = 'right';
                } else {
                    $position = 'bottom';
                }
            }
            $parameters['thumbnailPosition'] = $position;
        }

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetBulletTransition(this, ' . json_encode($parameters) . ');');

        $fullSize = intval($params->get(self::$key . 'bar-full-size'));

        return N2Html::tag("div", $displayAttributes + $attributes + array(
                "class" => $displayClass . ' n2-flex n2-ss-control-bullet n2-ss-control-bullet-' . $orientation . ($fullSize ? ' n2-ss-control-bullet-fullsize' : ''),
                "style" => $style
            ), N2HTML::tag("div", array(
            "class" => $barStyle . " nextend-bullet-bar n2-ow n2-bar-justify-content-" . $params->get(self::$key . 'align')
        ), ''));
    }

    public function prepareExport($export, $params) {
        $export->addVisual($params->get(self::$key . 'style'));
        $export->addVisual($params->get(self::$key . 'bar'));
        $export->addVisual($params->get(self::$key . 'font'));
    }

    public function prepareImport($import, $params) {

        $params->set(self::$key . 'style', $import->fixSection($params->get(self::$key . 'style')));
        $params->set(self::$key . 'bar', $import->fixSection($params->get(self::$key . 'bar')));
        $params->set(self::$key . 'font', $import->fixSection($params->get(self::$key . 'font')));
    }
}

N2SmartSliderWidgets::addWidget('bullet', new N2SSPluginWidgetBulletNumbers);
