<?php
N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');

class N2SSPluginWidgetArrowImageBar extends N2SSPluginWidgetAbstract {

    private static $key = 'widget-arrow-';

    protected $name = 'imagebar';

    public function getDefaults() {
        return array(
            'widget-arrow-previous-position-mode'   => 'simple',
            'widget-arrow-previous-position-area'   => 2,
            'widget-arrow-previous-position-offset' => 0,
            'widget-arrow-next-position-mode'       => 'simple',
            'widget-arrow-next-position-area'       => 4,
            'widget-arrow-next-position-offset'     => 0,
            'widget-arrow-width'                    => 100,
            'widget-arrow-previous-color'           => 'ffffffcc',
            'widget-arrow-previous'                 => '$ss$/plugins/widgetarrow/imagebar/imagebar/previous/simple-horizontal.svg',
            'widget-arrow-mirror'                   => 1,
            'widget-arrow-next-color'               => 'ffffffcc',
            'widget-arrow-next'                     => '$ss$/plugins/widgetarrow/imagebar/imagebar/next/simple-horizontal.svg'
        );
    }

    /**
     * @param N2Form $form
     */
    public function renderFields($form) {
        $settings = new N2Tab($form, 'image');
        new N2ElementNumber($settings, 'widget-arrow-width', n2_('Width'), 0, array(
            'style' => 'width:40px;',
            'unit'  => 'px'
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
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'imagebar' . DIRECTORY_SEPARATOR;
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
        $return = array();
        $slider->addLess(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'imagebar' . DIRECTORY_SEPARATOR . 'style.n2less'), array(
            "sliderid" => $slider->elementId
        ));
        $slider->features->addInitCallback(N2Filesystem::readFile(N2Filesystem::translate(dirname(__FILE__) . '/imagebar/arrow.min.js')));
    

        $previous      = $params->get(self::$key . 'previous');
        $previousColor = $params->get(self::$key . 'previous-color');
        if ($params->get(self::$key . 'mirror')) {
            $next      = str_replace('imagebar/previous/', 'imagebar/next/', $previous);
            $nextColor = $previousColor;
        } else {
            $next      = $params->get(self::$key . 'next');
            $nextColor = $params->get(self::$key . 'next-color');
        }

        $return['previous'] = $this->getHTML($slider, $id, $params, 'previous', $previous, $previousColor);
        $return['next']     = $this->getHTML($slider, $id, $params, 'next', $next, $nextColor);

        $slider->exposeSlideData['thumbnail'] = true;
        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetArrowImageBar(this);');

        return $return;
    }

    /**
     * @param N2SmartSlider $slider
     * @param               $id
     * @param               $params
     * @param               $side
     *
     * @return string
     */
    private function getHTML($slider, $id, &$params, $side, $image, $color) {

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

        switch ($side) {
            case 'previous':
                $backgroundImage = $slider->getPreviousSlide()
                                          ->getThumbnail();
                break;
            case 'next':
                $backgroundImage = $slider->getNextSlide()
                                          ->getThumbnail();
                break;
        }

        $style .= 'width: ' . intval($params->get(self::$key . 'width')) . 'px';

        $label = '';
        switch ($side) {
            case 'previous':
                $label = 'Previous slide';
                break;
            case 'next':
                $label = 'Next slide';
                break;
        }

        $isNormalFlow = self::isNormalFlow($params, self::$key . $side . '-');

        return N2Html::tag('div', $displayAttributes + $attributes + array(
                'id'         => $id . '-arrow-' . $side,
                'class'      => $displayClass . 'nextend-arrow nextend-arrow-imagebar n2-ow nextend-arrow-' . $side . ($isNormalFlow ? '' : ' n2-ib'),
                'style'      => $style,
                'role'       => 'button',
                'aria-label' => $label,
                'tabindex'   => '0'
            ), N2Html::tag('div', array(
                'class' => 'nextend-arrow-image n2-ow',
                'style' => 'background-image: URL(' . $backgroundImage . ');'
            ), '') . N2Html::tag('div', array(
                'class' => 'nextend-arrow-arrow n2-ow',
                'style' => 'background-image: URL(' . $image . ');'
            ), ''));
    }
}

N2SmartSliderWidgets::addWidget('arrow', new N2SSPluginWidgetArrowImageBar);
