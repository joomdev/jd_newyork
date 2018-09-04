<?php
N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');

class N2SSPluginWidgetArrowGrow extends N2SSPluginWidgetAbstract {

    private static $key = 'widget-arrow-';

    protected $name = 'grow';

    public function getDefaults() {
        return array(
            'widget-arrow-previous-position-mode'   => 'simple',
            'widget-arrow-previous-position-area'   => 6,
            'widget-arrow-previous-position-offset' => 15,
            'widget-arrow-next-position-mode'       => 'simple',
            'widget-arrow-next-position-area'       => 7,
            'widget-arrow-next-position-offset'     => 15,
            'widget-arrow-style'                    => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siYmFja2dyb3VuZGNvbG9yIjoiMDAwMDAwODAiLCJwYWRkaW5nIjoiM3wqfDN8KnwzfCp8M3wqfHB4IiwiYm94c2hhZG93IjoiMHwqfDB8KnwwfCp8MHwqfDAwMDAwMGZmIiwiYm9yZGVyIjoiMHwqfHNvbGlkfCp8MDAwMDAwZmYiLCJib3JkZXJyYWRpdXMiOiI1MCIsImV4dHJhIjoiIn0seyJiYWNrZ3JvdW5kY29sb3IiOiIwMWFkZDNmZiJ9XX0=',
            'widget-arrow-font'                     => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiJmZmZmZmZmZiIsInNpemUiOiIxMnx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJhZm9udCI6Ik1vbnRzZXJyYXQiLCJsaW5laGVpZ2h0IjoiMS4zIiwiYm9sZCI6MCwiaXRhbGljIjowLCJ1bmRlcmxpbmUiOjAsImFsaWduIjoibGVmdCIsImV4dHJhIjoiIn0se31dfQ==',
            'widget-arrow-animation-delay'          => 0,
            'widget-arrow-previous-color'           => 'ffffffcc',
            'widget-arrow-previous'                 => '$ss$/plugins/widgetarrow/grow/grow/previous/simple-horizontal.svg',
            'widget-arrow-mirror'                   => 1,
            'widget-arrow-next-color'               => 'ffffffcc',
            'widget-arrow-next'                     => '$ss$/plugins/widgetarrow/grow/grow/next/simple-horizontal.svg'
        );
    }

    /**
     * @param N2Form $form
     */
    public function renderFields($form) {
        $settings = new N2Tab($form, 'arrow-grow');

        new N2ElementStyle($settings, 'widget-arrow-style', n2_('Style'), '', array(
            'set'         => 1900,
            'previewMode' => 'button',
            'preview'     => '<div><div class="{styleClassName}" style="display: inline-block;"><img style="display: block;" src="{nextend.imageHelper.fixed($(\'#sliderwidget-arrow-previous-image\').val() || N2Color.colorizeSVG($(\'[data-image="\'+$(\'#sliderwidget-arrow-previous\').val()+\'"]\').attr(\'src\'), $(\'#sliderwidget-arrow-previous-color\').val()));}" /></div></div>'
        ));
        new N2ElementFont($settings, 'widget-arrow-font', n2_('Font'), '', array(
            'set'         => 1900,
            'previewMode' => 'link',
            'preview'     => '<div class="{fontClassName}"><a href="#" class="{styleClassName}" style="line-height: 48px;display:inline-block;" onclick="return false;">Slide title</a></div>',
            'style'       => 'sliderwidget-arrow-style'
        ));

        $previous = new N2ElementGroup($settings, 'arrow-previous', n2_('Previous'));
        new N2ElementImageListFromFolder($previous, 'widget-arrow-previous', n2_('Shape'), '', array(
            'post'       => 'break',
            'folder'     => N2Filesystem::translate($this->getPath() . 'previous/'),
            'isRequired' => true
        ));
        new N2ElementColor($previous, 'widget-arrow-previous-color', n2_('Color'), '', array(
            'alpha' => true
        ));

        new N2ElementOnoff($settings, 'widget-arrow-mirror', n2_('Mirror'), '', array(
            'rowClass'      => 'n2-expert',
            'isEnable'      => false,
            'relatedFields' => array(
                'arrow-next'
            )
        ));

        $next = new N2ElementGroup($settings, 'arrow-next', n2_('Next'), array(
            'rowClass' => 'n2-expert',
        ));
        new N2ElementImageListFromFolder($next, 'widget-arrow-next', n2_('Shape'), '', array(
            'post'       => 'break',
            'folder'     => N2Filesystem::translate($this->getPath() . 'next/'),
            'isRequired' => true
        ));
        new N2ElementColor($next, 'widget-arrow-next-color', n2_('Color'), '', array(
            'alpha' => true
        ));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'grow' . DIRECTORY_SEPARATOR;
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

        $slider->addLess(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'grow' . DIRECTORY_SEPARATOR . 'style.n2less'), array(
            "sliderid" => $slider->elementId
        ));
        $slider->features->addInitCallback(N2Filesystem::readFile(N2Filesystem::translate(dirname(__FILE__) . '/grow/arrow.min.js')));
    

        $previous      = $params->get(self::$key . 'previous');
        $previousColor = $params->get(self::$key . 'previous-color');
        if ($params->get(self::$key . 'mirror')) {
            $next      = str_replace('grow/previous/', 'grow/next/', $previous);
            $nextColor = $previousColor;
        } else {
            $next      = $params->get(self::$key . 'next');
            $nextColor = $params->get(self::$key . 'next-color');
        }

        $fontClass  = $slider->addFont($params->get(self::$key . 'font'), 'hover');
        $styleClass = $slider->addStyle($params->get(self::$key . 'style'), 'heading');

        $return['previous'] = $this->getHTML($slider, $id, $params, 'previous', $previous, $fontClass, $styleClass, $previousColor);
        $return['next']     = $this->getHTML($slider, $id, $params, 'next', $next, $fontClass, $styleClass, $nextColor);


        $slider->exposeSlideData['title'] = true;

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetArrowGrow(this, ' . $params->get(self::$key . 'animation-delay') . ');');

        return $return;
    }

    private function getHTML($slider, $id, &$params, $side, $image, $fontClass, $styleClass, $color) {

        list($displayClass, $displayAttributes) = self::getDisplayAttributes($params, self::$key);

        list($style, $attributes) = self::getPosition($params, self::$key . $side . '-');

        $ext = pathinfo($image, PATHINFO_EXTENSION);
        if (substr($image, 0, 1) == '$' && $ext == 'svg') {

            list($color, $opacity) = N2Color::colorToSVG($color);
            $image = 'data:image/svg+xml;base64,' . n2_base64_encode(str_replace(array(
                    'fill="#FFF"',
                    'opacity="1"'
                ), array(
                    'fill="#' . $color . '"',
                    'opacity="' . $opacity . '"'
                ), N2Filesystem::readFile(N2ImageHelper::fixed($image, true))));
        } else {
            $image = N2ImageHelper::fixed($image);
        }

        $label = '';
        switch ($side) {
            case 'previous':
                $title = $slider->getPreviousSlide()
                                ->getTitle();
                $label = 'Previous slide';
                break;
            case 'next':
                $title = $slider->getNextSlide()
                                ->getTitle();
                $label = 'Next slide';
                break;
        }

        $isNormalFlow = self::isNormalFlow($params, self::$key . $side . '-');

        return N2Html::tag('div', $displayAttributes + $attributes + array(
                'id'         => $id . '-arrow-' . $side,
                'class'      => $displayClass . $styleClass . 'nextend-arrow n2-ow nextend-arrow-grow nextend-arrow-' . $side . ($isNormalFlow ? '' : ' n2-ib'),
                'style'      => $style,
                'role'       => 'button',
                'aria-label' => $label,
                'tabindex'   => '0'
            ), N2Html::tag('div', array(
                'class' => $fontClass . ' nextend-arrow-title n2-ow'
            ), $title) . N2Html::tag('div', array(
                'class' => 'nextend-arrow-arrow n2-ow',
                'style' => 'background-image: URL(' . $image . ');'
            ), ''));
    }

    public function prepareExport($export, $params) {
        $export->addVisual($params->get(self::$key . 'style'));
        $export->addVisual($params->get(self::$key . 'font'));
    }

    public function prepareImport($import, $params) {

        $params->set(self::$key . 'style', $import->fixSection($params->get(self::$key . 'style', '')));
        $params->set(self::$key . 'font', $import->fixSection($params->get(self::$key . 'font', '')));
    }
}

N2SmartSliderWidgets::addWidget('arrow', new N2SSPluginWidgetArrowGrow);
