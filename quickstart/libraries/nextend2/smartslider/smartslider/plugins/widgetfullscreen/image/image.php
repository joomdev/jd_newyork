<?php
N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');

class N2SSPluginWidgetFullScreenImage extends N2SSPluginWidgetAbstract {

    private static $key = 'widget-fullscreen-';

    protected $name = 'image';

    public function getDefaults() {
        return array(
            'widget-fullscreen-responsive-desktop' => 1,
            'widget-fullscreen-responsive-tablet'  => 0.7,
            'widget-fullscreen-responsive-mobile'  => 0.5,
            'widget-fullscreen-tonormal-image'     => '',
            'widget-fullscreen-tonormal-color'     => 'ffffffcc',
            'widget-fullscreen-tonormal'           => '$ss$/plugins/widgetfullscreen/image/image/tonormal/full1.svg',
            'widget-fullscreen-style'              => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwYWIiLCJwYWRkaW5nIjoiMTB8KnwxMHwqfDEwfCp8MTB8KnxweCIsImJveHNoYWRvdyI6IjB8KnwwfCp8MHwqfDB8KnwwMDAwMDBmZiIsImJvcmRlciI6IjB8Knxzb2xpZHwqfDAwMDAwMGZmIiwiYm9yZGVycmFkaXVzIjoiMyIsImV4dHJhIjoiIn0seyJiYWNrZ3JvdW5kY29sb3IiOiIwMDAwMDBhYiJ9XX0=',
            'widget-fullscreen-position-mode'      => 'simple',
            'widget-fullscreen-position-area'      => 4,
            'widget-fullscreen-position-offset'    => 15,
            'widget-fullscreen-mirror'             => 1,
            'widget-fullscreen-tofull-image'       => '',
            'widget-fullscreen-tofull-color'       => 'ffffffcc',
            'widget-fullscreen-tofull'             => '$ss$/plugins/widgetfullscreen/image/image/tofull/full1.svg'
        );
    }


    public function renderFields($form) {
        $settings = new N2Tab($form, 'fullscreen');

        $responsive = new N2ElementGroup($settings, 'fullscreen-responsive-scale', n2_('Responsive scale'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementNumber($responsive, 'widget-fullscreen-responsive-desktop', n2_('Desktop'), '', array(
            'style' => 'width:40px;'
        ));
        new N2ElementNumber($responsive, 'widget-fullscreen-responsive-tablet', n2_('Tablet'), '', array(
            'style' => 'width:40px;'
        ));
        new N2ElementNumber($responsive, 'widget-fullscreen-responsive-mobile', n2_('Mobile'), '', array(
            'style' => 'width:40px;'
        ));

        new N2ElementImage($settings, 'widget-fullscreen-tonormal-image', n2_('To normal image'), '', array(
            'rowClass' => 'n2-expert'
        ));


        $toNormal = new N2ElementGroup($settings, 'fullscreen-tonormal', n2_('Shape'));
        new N2ElementImageListFromFolder($toNormal, 'widget-fullscreen-tonormal', n2_('Shape'), '', array(
            'folder' => N2Filesystem::translate($this->getPath() . 'tonormal/'),
            'post'   => 'break'
        ));
        new N2ElementColor($toNormal, 'widget-fullscreen-tonormal-color', n2_('Color'), '', array(
            'alpha' => true
        ));


        new N2ElementStyle($settings, 'widget-fullscreen-style', n2_('Style'), '', array(
            'set'         => 1900,
            'previewMode' => 'button',
            'preview'     => '<div class="{styleClassName}" style="display: inline-block;"><img style="display: block;" src="{nextend.imageHelper.fixed($(\'#sliderwidget-fullscreen-tonormal-image\').val() || $(\'[data-image="\'+$(\'#sliderwidget-fullscreen-tonormal\').val()+\'"]\').attr(\'src\'));}" /></div>'
        ));

        new N2ElementWidgetPosition($settings, 'widget-fullscreen-position', n2_('Position'));


        new N2ElementOnoff($settings, 'widget-fullscreen-mirror', n2_('Mirror'), '', array(
            'rowClass'      => 'n2-expert',
            'isEnable'      => false,
            'relatedFields' => array(
                'widget-fullscreen-tofull-image',
                'arrow-next'
            )
        ));

        new N2ElementImage($settings, 'widget-fullscreen-tofull-image', n2_('To fullscreen image'), '', array(
            'rowClass' => 'n2-expert'
        ));

        $next = new N2ElementGroup($settings, 'fullscreen-tofullscreen', n2_('To fullscreen'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementImageListFromFolder($next, 'widget-fullscreen-tofull', n2_('Shape'), '', array(
            'folder' => N2Filesystem::translate($this->getPath() . 'tofull/'),
            'post'   => 'break'
        ));
        new N2ElementColor($next, 'widget-fullscreen-tofull-color', n2_('Color'), '', array(
            'alpha' => true
        ));
    }


    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR;
    }

    public function getPositions(&$params) {
        $positions = array();

        $positions['fullscreen-position'] = array(
            self::$key . 'position-',
            'fullscreen'
        );

        return $positions;
    }

    public function render($slider, $id, $params) {
        $html = '';

        $toNormal      = $params->get(self::$key . 'tonormal-image');
        $toNormalColor = $params->get(self::$key . 'tonormal-color');
        if (empty($toNormal)) {
            $toNormal = $params->get(self::$key . 'tonormal');
            if ($toNormal == -1) {
                $toNormal = null;
            } else if ($toNormal[0] != '$') {
                $toNormal = N2Uri::pathToUri(dirname(__FILE__) . '/image/tonormal/' . $toNormal);
            }
        }

        if ($params->get(self::$key . 'mirror')) {
            $toFull      = str_replace('image/tonormal/', 'image/tofull/', $toNormal);
            $toFullColor = $toNormalColor;
        } else {
            $toFull      = $params->get(self::$key . 'tofull-image');
            $toFullColor = $params->get(self::$key . 'tofull-color');
            if (empty($toFull)) {
                $toFull = $params->get(self::$key . 'tofull');
                if ($toFull == -1) {
                    $toFull = null;
                } else if ($toFull[0] != '$') {
                    $toFull = N2Uri::pathToUri(dirname(__FILE__) . '/image/tofull/' . $toFull);
                }
            }
        }


        if ($toNormal && $toFull) {


            $ext = pathinfo($toNormal, PATHINFO_EXTENSION);
            if (substr($toNormal, 0, 1) == '$' && $ext == 'svg') {
                list($color, $opacity) = N2Color::colorToSVG($toNormalColor);
                $toNormal = 'data:image/svg+xml;base64,' . n2_base64_encode(str_replace(array(
                        'fill="#FFF"',
                        'opacity="1"'
                    ), array(
                        'fill="#' . $color . '"',
                        'opacity="' . $opacity . '"'
                    ), N2Filesystem::readFile(N2ImageHelper::fixed($toNormal, true))));
            } else {
                $toNormal = N2ImageHelper::fixed($toNormal);
            }

            $ext = pathinfo($toFull, PATHINFO_EXTENSION);
            if (substr($toFull, 0, 1) == '$' && $ext == 'svg') {
                list($color, $opacity) = N2Color::colorToSVG($toFullColor);
                $toFull = 'data:image/svg+xml;base64,' . n2_base64_encode(str_replace(array(
                        'fill="#FFF"',
                        'opacity="1"'
                    ), array(
                        'fill="#' . $color . '"',
                        'opacity="' . $opacity . '"'
                    ), N2Filesystem::readFile(N2ImageHelper::fixed($toFull, true))));
            } else {
                $toFull = N2ImageHelper::fixed($toFull);
            }

            $slider->addLess(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR . 'style.n2less'), array(
                "sliderid" => $slider->elementId
            ));
            $slider->features->addInitCallback(N2Filesystem::readFile(N2Filesystem::translate(dirname(__FILE__) . '/image/fullscreen.min.js')));
        

            list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

            $styleClass = $slider->addStyle($params->get(self::$key . 'style'), 'heading');

            $isNormalFlow = self::isNormalFlow($params, self::$key);
            list($style, $attributes) = self::getPosition($params, self::$key);


            $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetFullScreenImage(this, ' . n2_floatval($params->get(self::$key . 'responsive-desktop')) . ', ' . n2_floatval($params->get(self::$key . 'responsive-tablet')) . ', ' . n2_floatval($params->get(self::$key . 'responsive-mobile')) . ');');

            $html = N2Html::tag('div', $displayAttributes + $attributes + array(
                    'class' => $displayClass . $styleClass . 'n2-full-screen-widget n2-ow n2-full-screen-widget-image nextend-fullscreen ' . ($isNormalFlow ? '' : 'n2-ib'),
                    'style' => $style . ($isNormalFlow ? 'margin-left:auto;margin-right:auto;' : '')
                ), N2Html::image($toNormal, 'Full screen', array(
                        'class'    => 'n2-full-screen-widget-to-normal n2-ow',
                        'tabindex' => '0'
                    ) + N2Html::getExcludeLazyLoadAttributes()) . N2Html::image($toFull, 'Exit full screen', array(
                        'class'    => 'n2-full-screen-widget-to-full n2-ow',
                        'tabindex' => '0'
                    ) + N2Html::getExcludeLazyLoadAttributes()));
        }

        return $html;
    }

    public function prepareExport($export, $params) {
        $export->addImage($params->get(self::$key . 'tonormal-image', ''));
        $export->addImage($params->get(self::$key . 'tofull-image', ''));

        $export->addVisual($params->get(self::$key . 'style'));
    }

    public function prepareImport($import, $params) {

        $params->set(self::$key . 'tonormal-image', $import->fixImage($params->get(self::$key . 'tonormal-image', '')));
        $params->set(self::$key . 'tofull-image', $import->fixImage($params->get(self::$key . 'tofull-image', '')));

        $params->set(self::$key . 'style', $import->fixSection($params->get(self::$key . 'style', '')));
    }

}

N2SmartSliderWidgets::addWidget('fullscreen', new N2SSPluginWidgetFullScreenImage);

class N2SSPluginWidgetFullScreenImageBlue extends N2SSPluginWidgetFullScreenImage {

    protected $name = 'imageBlue';

    public function getDefaults() {
        return array_merge(parent::getDefaults(), array(
            'widget-fullscreen-tonormal' => '$ss$/plugins/widgetfullscreen/image/image/tonormal/full2.svg',
            'widget-fullscreen-tofull'   => '$ss$/plugins/widgetfullscreen/image/image/tofull/full2.svg',
            'widget-fullscreen-style'    => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwYWIiLCJwYWRkaW5nIjoiMTB8KnwxMHwqfDEwfCp8MTB8KnxweCIsImJveHNoYWRvdyI6IjB8KnwwfCp8MHwqfDB8KnwwMDAwMDBmZiIsImJvcmRlciI6IjB8Knxzb2xpZHwqfDAwMDAwMGZmIiwiYm9yZGVycmFkaXVzIjoiMyIsImV4dHJhIjoiIn0seyJiYWNrZ3JvdW5kY29sb3IiOiIwMGMxYzRmZiJ9XX0='
        ));
    }
}

N2SmartSliderWidgets::addWidget('fullscreen', new N2SSPluginWidgetFullScreenImageBlue);
