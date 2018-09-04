<?php
N2Loader::import('libraries.plugins.N2SliderWidgetAbstract', 'smartslider');

class N2SSPluginWidgetArrowReveal extends N2SSPluginWidgetAbstract {

    private static $key = 'widget-arrow-';

    protected $name = 'reveal';

    public function getDefaults() {
        return array(
            'widget-arrow-previous-position-mode'   => 'simple',
            'widget-arrow-previous-position-area'   => 6,
            'widget-arrow-previous-position-offset' => 0,
            'widget-arrow-next-position-mode'       => 'simple',
            'widget-arrow-next-position-area'       => 7,
            'widget-arrow-next-position-offset'     => 0,
            'widget-arrow-font'                     => '',
            'widget-arrow-background'               => '00000080',
            'widget-arrow-title-show'               => 0,
            'widget-arrow-title-font'               => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siY29sb3IiOiJmZmZmZmZmZiIsInNpemUiOiIxMnx8cHgiLCJ0c2hhZG93IjoiMHwqfDB8KnwwfCp8MDAwMDAwZmYiLCJhZm9udCI6Ik1vbnRzZXJyYXQiLCJsaW5laGVpZ2h0IjoiMS4zIiwiYm9sZCI6MCwiaXRhbGljIjowLCJ1bmRlcmxpbmUiOjAsImFsaWduIjoibGVmdCIsImV4dHJhIjoiIn0se31dfQ==',
            'widget-arrow-title-background'         => '000000cc',
            'widget-arrow-animation'                => 'slide',
            'widget-arrow-previous-color'           => 'ffffffcc',
            'widget-arrow-previous'                 => '$ss$/plugins/widgetarrow/reveal/reveal/previous/simple-horizontal.svg',
            'widget-arrow-mirror'                   => 1,
            'widget-arrow-next-color'               => 'ffffffcc',
            'widget-arrow-next'                     => '$ss$/plugins/widgetarrow/reveal/reveal/next/simple-horizontal.svg'
        );
    }

    /**
     * @param N2Form $form
     */
    public function renderFields($form) {
        $settings = new N2Tab($form, 'arrow-settings');

        new N2ElementColor($settings, 'widget-arrow-background', n2_('Background'), '', array(
            'alpha' => true
        ));

        $title = new N2ElementGroup($settings, 'arrow-title', n2_('Slide title'));
        new N2ElementOnOff($title, 'widget-arrow-title-show', n2_('Enable'), 0, array(
            'relatedFields' => array(
                'widget-arrow-title-font',
                'widget-arrow-title-background'
            )
        ));
        new N2ElementFont($title, 'widget-arrow-title-font', n2_('Font'), '', array(
            'previewMode' => 'link',
            'set'         => 1900,
            'preview'     => '<div class="{fontClassName}"><a href="#" onclick="return false;">' . n2_('Slide title') . '</a></div>'
        ));
        new N2ElementColor($title, 'widget-arrow-title-background', n2_('Background color'), '', array(
            'alpha' => true
        ));

        new N2ElementRadio($settings, 'widget-arrow-animation', n2_('Animation'), '', array(
            'options' => array(
                'slide' => n2_('Slide'),
                'fade'  => n2_('Fade'),
                'turn'  => n2_('Turn')
            )
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
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'reveal' . DIRECTORY_SEPARATOR;
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

        list($hex, $RGBA) = N2Color::colorToCss($params->get(self::$key . 'background'));
        list($titleHex, $titleRGBA) = N2Color::colorToCss($params->get(self::$key . 'title-background'));


        $slider->addLess(N2Filesystem::translate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'reveal' . DIRECTORY_SEPARATOR . 'style.n2less'), array(
            "sliderid"            => $slider->elementId,
            "arrowBackgroundHex"  => $hex ? '#' . $hex : 'transparent',
            "arrowBackgroundRGBA" => $RGBA,
            "titleBackgroundHex"  => $titleHex ? '#' . $titleHex : 'transparent',
            "titleBackgroundRGBA" => $titleRGBA
        ));
        $slider->features->addInitCallback(N2Filesystem::readFile(N2Filesystem::translate(dirname(__FILE__) . '/reveal/arrow.min.js')));
    

        $previous      = $params->get(self::$key . 'previous');
        $previousColor = $params->get(self::$key . 'previous-color');
        if ($params->get(self::$key . 'mirror')) {
            $next      = str_replace('reveal/previous/', 'reveal/next/', $previous);
            $nextColor = $previousColor;
        } else {
            $next      = $params->get(self::$key . 'next');
            $nextColor = $params->get(self::$key . 'next-color');
        }

        $fontClass = $slider->addFont($params->get(self::$key . 'title-font'), 'simple');

        $animation      = $params->get(self::$key . 'animation');
        $animationClass = ' n2-ss-arrow-animation-' . $animation;

        $return['previous'] = $this->getHTML($slider, $id, $params, 'previous', $previous, $fontClass, $animationClass, $previousColor);
        $return['next']     = $this->getHTML($slider, $id, $params, 'next', $next, $fontClass, $animationClass, $nextColor);

        $slider->exposeSlideData['title']     = true;
        $slider->exposeSlideData['thumbnail'] = true;

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetArrowReveal(this,"' . $animation . '");');

        return $return;
    }

    /**
     * @param N2SmartSlider $slider
     * @param               $id
     * @param               $params
     * @param               $side
     * @param               $image
     * @param               $fontClass
     * @param               $animationClass
     *
     * @return string
     */
    private function getHTML($slider, $id, &$params, $side, $image, $fontClass, $animationClass, $color) {

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
                $title           = $slider->getPreviousSlide()
                                          ->getTitle();
                break;
            case 'next':
                $backgroundImage = $slider->getNextSlide()
                                          ->getThumbnail();
                $title           = $slider->getNextSlide()
                                          ->getTitle();
                break;
        }

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
                'class'      => $displayClass . 'nextend-arrow n2-ow nextend-arrow-reveal nextend-arrow-' . $side . $animationClass . ($isNormalFlow ? '' : ' n2-ib'),
                'style'      => $style,
                'role'       => 'button',
                'aria-label' => $label,
                'tabindex'   => '0'
            ), N2Html::tag('div', array(
                'class' => ' nextend-arrow-image n2-ow',
                'style' => 'background-image: URL(' . $backgroundImage . ');'
            ), $params->get(self::$key . 'title-show') ? N2Html::tag('div', array(
                'class' => $fontClass . ' nextend-arrow-title n2-ow'
            ), $title) : '') . N2Html::tag('div', array(
                'class' => 'nextend-arrow-arrow n2-ow',
                'style' => 'background-image: URL(' . $image . ');'
            ), ''));
    }

    public function prepareExport($export, $params) {
        $export->addVisual($params->get(self::$key . 'title-font'));
    }

    public function prepareImport($import, $params) {

        $params->set(self::$key . 'title-font', $import->fixSection($params->get(self::$key . 'title-font', '')));
    }
}

N2SmartSliderWidgets::addWidget('arrow', new N2SSPluginWidgetArrowReveal);
