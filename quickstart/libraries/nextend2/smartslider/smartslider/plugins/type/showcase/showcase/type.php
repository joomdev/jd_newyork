<?php

class N2SmartSliderTypeShowcase extends N2SmartSliderType {

    private $direction = 'horizontal';

    public function getDefaults() {
        return array(
            'slide-width'         => 600,
            'slide-height'        => 400,
            'background'          => '',
            'background-size'     => 'cover',
            'background-fixed'    => 0,
            'border-width'        => 0,
            'border-color'        => '3E3E3Eff',
            'border-radius'       => 0,
            'slider-css'          => '',
            'slide-css'           => '',
            'animation-duration'  => 800,
            'animation-delay'     => 0,
            'animation-easing'    => 'easeOutQuad',
            'animation-direction' => 'horizontal',
            'slide-distance'      => 60,
            'perspective'         => 1000,
            'carousel'            => 1,
            'carousel-slides'     => 3,
            'opacity'             => '0|*|100|*|100|*|100',
            'scale'               => '0|*|100|*|100|*|100',
            'translate-x'         => '0|*|0|*|0|*|0',
            'translate-y'         => '0|*|0|*|0|*|0',
            'translate-z'         => '0|*|0|*|0|*|0',
            'rotate-x'            => '0|*|0|*|0|*|0',
            'rotate-y'            => '0|*|0|*|0|*|0',
            'rotate-z'            => '0|*|0|*|0|*|0'
        );
    }

    protected function renderType($css) {

        $params = $this->slider->params;
        N2JS::addStaticGroup(N2Filesystem::translate(dirname(__FILE__)) . '/dist/smartslider-showcase-type-frontend.min.js', 'smartslider-showcase-type-frontend');
    

        $this->jsDependency[] = 'smartslider-showcase-type-frontend';

        $background = $params->get('background');
        $sliderCSS  = $params->get('slider-css');
        if (!empty($background)) {
            $sliderCSS = 'background-image: URL(' . N2ImageHelper::fixed($background) . ');';
        }

        $this->initParticleJS();

        echo $this->openSliderElement();
        $this->widgets->echoAbove();
        ?>
        <div class="n2-ss-slider-1 n2-ss-swipe-element n2-ow">
            <div class="n2-ss-slider-2 n2-ow" style="<?php echo $sliderCSS; ?>">
                <div class="n2-ss-slider-3 n2-ow">
                    <?php
                    echo $this->slider->staticHtml;
                    ?>
                    <div class="n2-ss-showcase-slides n2-ow"><?php
                        foreach ($this->slider->slides AS $i => $slide) {
                            $slide->finalize();

                            echo N2Html::tag('div', N2HTML::mergeAttributes($slide->attributes, array(
                                'class' => 'n2-ss-slide ' . $slide->classes . ' n2-ss-canvas n2-ow',
                                'style' => $slide->style . $params->get('slide-css')
                            )), $slide->background . N2Html::tag('div', array('class' => 'n2-ss-slide-inner') + $slide->linkAttributes, $slide->getHTML()) . N2Html::tag('div', array('class' => 'n2-ss-showcase-overlay n2-ow')));
                        }
                        ?></div>
                </div>
                <?php
                $this->renderShapeDividers();
                ?>
            </div>
            <?php
            $this->widgets->echoRemainder();
            ?>
        </div>
        <?php
        $this->widgets->echoBelow();
        echo N2Html::closeTag('div');

        $this->javaScriptProperties['carousel']           = intval($params->get('carousel'));
        $this->javaScriptProperties['carouselSideSlides'] = intval((max(intval($params->get('carousel-slides')), 1) - 1) / 2);

        $this->javaScriptProperties['showcase'] = array(
            'duration' => intval($params->get('animation-duration')),
            'delay'    => intval($params->get('animation-delay')),
            'ease'     => $params->get('animation-easing')
        );

        $this->javaScriptProperties['layerMode']['playOnce'] = 1;

        $this->initAnimationProperties();

        $this->style .= $css->getCSS();

        echo N2Html::clear();
    }

    public function getScript() {
        return "N2R(" . json_encode($this->jsDependency) . ",function(){new N2Classes.SmartSliderShowcase('#{$this->slider->elementId}', " . json_encode($this->javaScriptProperties) . ");});";
    }

    protected function getSliderClasses() {
        switch ($this->slider->params->get('animation-direction', 'horizontal')) {
            case 'vertical':
                $this->direction = 'vertical';

                return parent::getSliderClasses() . ' n2-ss-showcase-vertical';
                break;
            default:
                $this->direction = 'horizontal';

                return parent::getSliderClasses() . ' n2-ss-showcase-horizontal';
        }
    }

    private function initAnimationProperties() {
        $params = $this->slider->params;

        $slideDistance = intval($params->get('slide-distance'));
        if ($slideDistance == 0) {
            $slideDistance = -1;
        }

        $this->javaScriptProperties['showcase'] += array(
            'direction' => $this->direction,
            'distance'  => $slideDistance,
            'animate'   => array(
                'opacity'   => self::animationPropertyState($params, 'opacity', 100),
                'scale'     => self::animationPropertyState($params, 'scale', 100),
                'x'         => self::animationPropertyState($params, 'translate-x'),
                'y'         => self::animationPropertyState($params, 'translate-y'),
                'z'         => self::animationPropertyState($params, 'translate-z'),
                'rotationX' => self::animationPropertyState($params, 'rotate-x'),
                'rotationY' => self::animationPropertyState($params, 'rotate-y'),
                'rotationZ' => self::animationPropertyState($params, 'rotate-z'),
            )
        );
    }

    private static function animationPropertyState($params, $prop, $normalize = 1) {
        $propValue = N2Parse::parse($params->get($prop));
        if ($propValue[0] != 1) {
            return null;
        }

        return array(
            'before' => intval($propValue[1]) / $normalize,
            'active' => intval($propValue[2]) / $normalize,
            'after'  => intval($propValue[3]) / $normalize
        );
    }
}