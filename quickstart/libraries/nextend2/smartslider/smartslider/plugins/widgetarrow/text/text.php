<?php
N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSPluginWidgetArrowText extends N2SSPluginWidgetAbstract {

    private static $key = 'widget-arrow-';

    protected $name = 'text';

    public function getDefaults() {
        return array(
            'widget-arrow-style'                    => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwYWIiLCJwYWRkaW5nIjoiOHwqfDEwfCp8OHwqfDEwfCp8cHgiLCJib3hzaGFkb3ciOiIwfCp8MHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJib3JkZXIiOiIwfCp8c29saWR8KnwwMDAwMDBmZiIsImJvcmRlcnJhZGl1cyI6IjMiLCJleHRyYSI6IiJ9LHsiYmFja2dyb3VuZGNvbG9yIjoiNWNiYTNjZmYifV19',
            'widget-arrow-font'                     => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiJmZmZmZmZmZiIsInNpemUiOiIxMnx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJhZm9udCI6Ik1vbnRzZXJyYXQiLCJsaW5laGVpZ2h0IjoiMS4zIiwiYm9sZCI6MCwiaXRhbGljIjowLCJ1bmRlcmxpbmUiOjAsImFsaWduIjoibGVmdCIsImV4dHJhIjoiIn0se31dfQ==',
            'widget-arrow-previous-position-mode'   => 'simple',
            'widget-arrow-previous-position-area'   => 6,
            'widget-arrow-previous-position-offset' => 15,
            'widget-arrow-next-position-mode'       => 'simple',
            'widget-arrow-next-position-area'       => 7,
            'widget-arrow-next-position-offset'     => 15,
            'widget-arrow-previous-label'           => n2_('PREV'),
            'widget-arrow-next-label'               => n2_('NEXT')
        );
    }

    /**
     * @param N2Form $form
     */
    public function renderFields($form) {
        $settings = new N2Tab($form, 'arrow');
        new N2ElementStyle($settings, 'widget-arrow-style', n2_('Style'), '', array(
            'previewMode' => 'button',
            'set'         => 1900,
            'font'        => 'sliderwidget-arrow-font',
            'preview'     => '<div class="{fontClassName}"><a href="#" class="{styleClassName}" onclick="return false;">{$(\'#sliderwidget-arrow-previous-label\').val();}</a></div>'
        ));

        new N2ElementFont($settings, 'widget-arrow-font', n2_('Font'), '', array(
            'previewMode' => 'link',
            'set'         => 1900,
            'style'       => 'sliderwidget-arrow-style',
            'preview'     => '<div class="{fontClassName}"><a href="#" class="{styleClassName}" onclick="return false;">{$(\'#sliderwidget-arrow-previous-label\').val();}</a></div>'
        ));

        new N2ElementWidgetPosition($settings, 'widget-arrow-previous-position', n2_('Previous position'));
        new N2ElementText($settings, 'widget-arrow-previous-label', n2_('Previous label'));

        new N2ElementWidgetPosition($settings, 'widget-arrow-next-position', n2_('Next position'));
        new N2ElementText($settings, 'widget-arrow-next-label', n2_('Next label'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'text' . DIRECTORY_SEPARATOR;
    }

    public function getPositions(&$params) {
        $positions = array();

        $positions['previous-position'] = array(
            self::$key . 'previous-position-',
            'previous'
        );

        $positions['next-position'] = array(
            self::$key . 'next-position-',
            'next'
        );

        return $positions;
    }

    public function render($slider, $id, $params) {
        if (count($slider->slides) <= 1) {
            return '';
        }

        list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

        $slider->addLess(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'text' . DIRECTORY_SEPARATOR . 'style.n2less'), array(
            "sliderid" => $slider->elementId
        ));

        $font  = $slider->addFont($params->get(self::$key . 'font'), 'link');
        $style = $slider->addStyle($params->get(self::$key . 'style'), 'heading');

        return array(
            'previous' => $this->getHtml($params, 'previous', $displayClass, $displayAttributes, $font, $style, "window['" . $id . "'][n2const.rtl.previous]();return false;"),
            'next'     => $this->getHtml($params, 'next', $displayClass, $displayAttributes, $font, $style, "window['" . $id . "'][n2const.rtl.next]();return false;")
        );
    }

    private function getHtml(&$params, $side, $displayClass, $displayAttributes, $font, $styleClass, $onClick) {

        $isNormalFlow = self::isNormalFlow($params, self::$key . $side . '-');
        list($style, $attributes) = self::getPosition($params, self::$key . $side . '-');

        $label = '';
        switch ($side) {
            case 'previous':
                $label = 'Previous slide';
                break;
            case 'next':
                $label = 'Next slide';
                break;
        }

        $html = N2Html::openTag("div", $displayAttributes + $attributes + array(
                "class"      => $displayClass . " n2-ow nextend-arrow nextend-arrow-{$side} {$font} " . ($isNormalFlow ? '' : 'n2-ib'),
                "style"      => $style . ($isNormalFlow ? 'text-align:center;' : ''),
                'role'       => 'button',
                'aria-label' => $label
            ));


        $html .= N2Html::link($params->get(self::$key . $side . '-label'), '#', array(
            "onclick" => $onClick,
            "class"   => $styleClass . ' n2-ow' . ($isNormalFlow ? '' : ' n2-ib'),
            "style"   => 'display:inline-block'
        ));

        $html .= N2Html::closeTag("div");

        return $html;
    }

    public function prepareExport($export, $params) {
        $export->addVisual($params->get(self::$key . 'font'));
        $export->addVisual($params->get(self::$key . 'style'));
    }

    public function prepareImport($import, $params) {

        $params->set(self::$key . 'font', $import->fixSection($params->get(self::$key . 'font', '')));
        $params->set(self::$key . 'style', $import->fixSection($params->get(self::$key . 'style', '')));
    }

}

N2SmartSliderWidgets::addWidget('arrow', new N2SSPluginWidgetArrowText);
