<?php

abstract class N2SmartSliderType {

    /**
     * @var N2SmartSliderAbstract
     */
    protected $slider;

    protected $jsDependency = array(
        'nextend-frontend',
        'smartslider-frontend'
    );

    protected $javaScriptProperties;

    /** @var  N2SmartSliderWidgets */
    protected $widgets;

    protected $shapeDividerAdded = false;

    protected $style = '';

    public function __construct($slider) {
        $this->slider = $slider;
        $this->jsDependency[] = 'nextend-gsap';
    

        if ($slider->isAdmin) {
            $this->jsDependency[] = 'documentReady';
        }
    }

    public static function getItemDefaults() {
        return array();
    }

    /**
     * @param N2SmartSliderCSSAbstract $css
     *
     * @return string
     */
    public function render($css) {

        $this->javaScriptProperties = $this->slider->features->generateJSProperties();

        $this->widgets = new N2SmartSliderWidgets($this->slider);

        ob_start();
        $this->renderType($css);

        return ob_get_clean();
    }

    /**
     * @param N2SmartSliderCSSAbstract $css
     *
     * @return string
     */
    protected abstract function renderType($css);

    protected function getSliderClasses() {
        return $this->slider->features->fadeOnLoad->getSliderClass();
    }

    protected function openSliderElement() {
        return N2Html::openTag('div', array(
                'id'           => $this->slider->elementId,
                'data-creator' => 'Smart Slider 3',
                'class'        => 'n2-ss-slider n2-ow n2-has-hover n2notransition ' . $this->getSliderClasses(),

            ) + $this->getFontSizeAttributes());
    }

    private function getFontSizeAttributes() {

        return $this->slider->features->responsive->getMinimumFontSizeAttributes() + array(
                'style'         => "font-size: " . $this->slider->fontSize . "px;",
                'data-fontsize' => $this->slider->fontSize
            );
    }

    public function getDefaults() {
        return array();
    }

    /**
     * @param $params N2Data
     */
    public function limitParams($params) {

    }

    protected function initParticleJS() {
        $particle = $this->slider->params->get('particle');
        if ($this->slider->isAdmin || empty($particle)) {
            return;
        }
        $particle = new N2Data($particle, true);
        $preset   = $particle->get('preset', '0');
        if ($preset != '0') {
            N2JS::addStaticGroup(NEXTEND_SMARTSLIDER_ASSETS . '/dist/particles.min.js', 'particles');
        

            $custom = $particle->get('custom', '');
            if ($preset == 'custom' && is_array($custom)) {
                $jsProp = $custom;
            } else {
                $jsProp = json_decode(N2Filesystem::readFile(NEXTEND_SMARTSLIDER_ASSETS . '/js/particlejs/presets/' . $particle->get('preset') . '.json'), true);

                $color                                   = N2Color::colorToSVG($particle->get('color'));
                $jsProp['particles']["color"]["value"]   = '#' . $color[0];
                $jsProp['particles']["opacity"]["value"] = $color[1];

                $lineColor                                     = N2Color::colorToSVG($particle->get('line-color'));
                $jsProp['particles']["line_linked"]["color"]   = '#' . $lineColor[0];
                $jsProp['particles']["line_linked"]["opacity"] = $lineColor[1];

                $hover = $particle->get('hover', 0);
                if ($hover == '0') {
                    $jsProp['interactivity']["events"]["onhover"]['enable'] = 0;
                } else {
                    $jsProp['interactivity']["events"]["onhover"]['enable'] = 1;
                    $jsProp['interactivity']["events"]["onhover"]['mode']   = $hover;
                }

                $click = $particle->get('click', 0);
                if ($click == '0') {
                    $jsProp['interactivity']["events"]["onclick"]['enable'] = 0;
                } else {
                    $jsProp['interactivity']["events"]["onclick"]['enable'] = 1;
                    $jsProp['interactivity']["events"]["onclick"]['mode']   = $click;
                }

                $jsProp['particles']["number"]["value"] = max(10, min(200, $particle->get('number')));

                $jsProp['particles']["move"]["speed"] = max(1, min(60, $particle->get('speed')));
            }

            $jsProp['mobile'] = intval($particle->get('mobile', 0));

            $this->javaScriptProperties['particlejs'] = $jsProp;
        }
    
    }

    protected function renderShapeDividers() {
        $shapeDividers = $this->slider->params->get('shape-divider');
        if (!empty($shapeDividers)) {
            $shapeDividers = json_decode($shapeDividers, true);
            if ($shapeDividers) {
                $this->renderShapeDivider('top', $shapeDividers['top']);
                $this->renderShapeDivider('bottom', $shapeDividers['bottom']);
            }
        }
    
    }

    private function renderShapeDivider($side, $params) {
        $data = new N2Data($params);
        $type = $data->get('type', "0");
        if ($type != "0") {
            preg_match('/([a-z]+)\-(.*)/', $type, $matches);

            $type = $matches[2];
            switch ($matches[1]) {
                case 'bi':
                    $type = 'bicolor/' . $type;
                    break;
            }

            $file = NEXTEND_SMARTSLIDER_ASSETS . '/shapedivider/' . $type . '.svg';
            if (N2Filesystem::existsFile($file)) {

                $animate = $data->get('animate') == '1';

                if ($animate) {
                    N2JS::addStaticGroup(N2LIBRARYASSETS . "/dist/MorphSVGPlugin.min.js", 'gsap-MorphSVGPlugin');
                
                }

                $outer = array(
                    'class'                       => 'n2-ss-shape-divider n2-ss-shape-divider-' . $side . ($data->get('flip') == '1' ? ' n2-ss-flip-horizontal' : '') . ($animate ? ' n2-ss-divider-animate' : ''),
                    'style'                       => 'height:' . $data->get('desktopportraitheight') . 'px;',
                    'data-desktopportraitheight'  => $data->get('desktopportraitheight'),
                    'data-desktoplandscapeheight' => $data->get('desktoplandscapeheight'),
                    'data-tabletportraitheight'   => $data->get('tabletportraitheight'),
                    'data-tabletlandscapeheight'  => $data->get('tabletlandscapeheight'),
                    'data-mobileportraitheight'   => $data->get('mobileportraitheight'),
                    'data-mobilelandscapeheight'  => $data->get('mobilelandscapeheight'),
                    'data-scroll'                 => $data->get('scroll', 0),
                    'data-speed'                  => $data->get('speed', 100),
                    'data-side'                   => $side
                );

                $inner = array(
                    'class'                      => 'n2-ss-shape-divider-inner',
                    'style'                      => 'width:' . $data->get('desktopportraitwidth') . '%;margin-left:' . (($data->get('desktopportraitwidth') - 100) / -2) . '%;',
                    'data-desktopportraitwidth'  => $data->get('desktopportraitwidth'),
                    'data-desktoplandscapewidth' => $data->get('desktoplandscapewidth'),
                    'data-tabletportraitwidth'   => $data->get('tabletportraitwidth'),
                    'data-tabletlandscapewidth'  => $data->get('tabletlandscapewidth'),
                    'data-mobileportraitwidth'   => $data->get('mobileportraitwidth'),
                    'data-mobilelandscapewidth'  => $data->get('mobilelandscapewidth')
                );

                $svg = N2Filesystem::readFile($file);
                if (!$animate) {
                    $svg = preg_replace('/<g.*?class="n2-ss-divider-start".*?<\/g>/s', '', $svg);
                }

                echo N2HTML::tag('div', $outer, N2HTML::tag('div', $inner, str_replace(array(
                    '#000000',
                    '#000010'
                ), array(
                    N2Color::colorToRGBA($data->get('color')),
                    N2Color::colorToRGBA($data->get('color2'))
                ), $svg)));

                if (!$this->shapeDividerAdded) {
                    $this->javaScriptProperties['initCallbacks'][] = file_get_contents(NEXTEND_SMARTSLIDER_ASSETS . "/dist/shapedivider.min.js");
                
                    $this->shapeDividerAdded = true;
                }
            }
        }
    
    }

    /**
     * @return string
     */
    public function getScript() {
        return '';
    }

    public function getStyle() {
        return $this->style;
    }

    public function setJavaScriptProperty($key, $value) {
        $this->javaScriptProperties[$key] = $value;
    }
}