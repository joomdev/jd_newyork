<?php

class N2SmartSliderTypeBlock extends N2SmartSliderType {

    public function getDefaults() {
        return array(
            'background'       => '',
            'background-size'  => 'cover',
            'background-fixed' => 0,
            'slider-css'       => '',

            'kenburns-animation' => ''
        );
    }

    protected function renderType($css) {

        $params = $this->slider->params;
        N2JS::addStaticGroup(N2Filesystem::translate(dirname(__FILE__)) . '/dist/smartslider-block-type-frontend.min.js', 'smartslider-block-type-frontend');
    

        $this->jsDependency[] = 'smartslider-block-type-frontend';

        $background = $params->get('background');
        $sliderCSS  = $params->get('slider-css');
        if (!empty($background)) {
            $sliderCSS = 'background-image: URL(' . N2ImageHelper::fixed($background) . ');';
        }

        $this->initParticleJS();

        echo $this->openSliderElement();
        $this->widgets->echoAbove();
        ?>

        <div class="n2-ss-slider-1 n2-ow" style="<?php echo $sliderCSS; ?>">
            <?php
            echo $this->getBackgroundVideo($params);
            ?>
            <div class="n2-ss-slider-2 n2-ow">
                <?php
                echo $this->slider->staticHtml;

                echo N2Html::tag('div', array('class' => 'n2-ss-slide-backgrounds'));

                $slide = $this->slider->slides[$this->slider->firstSlideIndex];

                $slide->finalize();

                echo N2Html::tag('div', N2HTML::mergeAttributes($slide->attributes, $slide->linkAttributes, array(
                    'class' => 'n2-ss-slide n2-ss-canvas n2-ow ' . $slide->classes,
                    'style' => $slide->style
                )), $slide->background . $slide->getHTML());
                ?>
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

        $this->style .= $css->getCSS();


        echo N2Html::clear();
    }

    public function getScript() {
        return "N2R(" . json_encode($this->jsDependency) . ",function(){new N2Classes.SmartSliderBlock('#{$this->slider->elementId}', " . json_encode($this->javaScriptProperties) . ");});";
    }

    private function getBackgroundVideo($params) {
        $mp4 = N2ImageHelper::fixed($params->get('backgroundVideoMp4', ''));

        if (empty($mp4)) {
            return '';
        }

        $sources = '';

        if ($mp4) {
            $sources .= N2Html::tag("source", array(
                "src"  => $mp4,
                "type" => "video/mp4"
            ), '', false);
        }

        $attributes = array();

        if ($params->get('backgroundVideoMuted', 1)) {
            $attributes['muted'] = 'muted';
        }

        if ($params->get('backgroundVideoLoop', 1)) {
            $attributes['loop'] = 'loop';
        }

        return N2Html::tag('div', array('class' => 'n2-ss-slider-background-video-container n2-ow'), N2Html::tag('video', $attributes + array(
                'class'              => 'n2-ss-slider-background-video n2-ow',
                'data-mode'          => $params->get('backgroundVideoMode', 'fill'),
                'playsinline'        => 1,
                'webkit-playsinline' => 1,
                'data-keepplaying'   => 1,
                'preload'            => 'none'
            ), $sources));

    }

    /**
     * @param $params N2Data
     */
    public function limitParams($params) {

        $params->loadArray(array(
            'controlsScroll'          => 0,
            'controlsDrag'            => 0,
            'controlsTouch'           => 0,
            'controlsKeyboard'        => 0,
            'controlsTilt'            => 0,
            'autoplay'                => 0,
            'autoplayStart'           => 0,
            'widgetarrow'             => 'disabled',
            'widgetbullet'            => 'disabled',
            'widgetautoplay'          => 'disabled',
            'widgetindicator'         => 'disabled',
            'widgetbar'               => 'disabled',
            'widgetthumbnail'         => 'disabled',
            'randomize'               => 0,
            'randomizeFirst'          => 0,
            'randomize-cache'         => 0,
            'maximumslidecount'       => 1,
            'imageload'               => 0,
            'imageloadNeighborSlides' => 0,
            'maintain-session'        => 0
        ));
    }
}

