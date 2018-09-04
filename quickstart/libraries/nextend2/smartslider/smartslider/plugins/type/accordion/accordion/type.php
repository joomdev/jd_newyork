<?php

class N2SmartSliderTypeAccordion extends N2SmartSliderType {

    public static function getItemDefaults() {
        return array(
            'align'  => 'left',
            'valign' => 'top'
        );
    }

    public function getDefaults() {
        return array(
            'orientation'         => 'horizontal',
            'carousel'            => 1,
            'outer-border'        => 6,
            'outer-border-color'  => '3E3E3Eff',
            'inner-border'        => 6,
            'inner-border-color'  => '222222ff',
            'border-radius'       => 6,
            'tab-normal-color'    => '3E3E3E',
            'tab-active-color'    => '87B801',
            'slide-margin'        => 2,
            'title-size'          => 30,
            'title-margin'        => 10,
            'title-border-radius' => 2,
            'title-font'          => 'eyJuYW1lIjoiU3RhdGljIiwiZGF0YSI6W3siZXh0cmEiOiJ0ZXh0LXRyYW5zZm9ybTogdXBwZXJjYXNlOyIsImNvbG9yIjoiZmZmZmZmZmYiLCJzaXplIjoiMTR8fHB4IiwidHNoYWRvdyI6IjB8KnwwfCp8MHwqfDAwMDAwMGZmIiwiYWZvbnQiOiJNb250c2VycmF0IiwibGluZWhlaWdodCI6IjEuMyIsImJvbGQiOjAsIml0YWxpYyI6MCwidW5kZXJsaW5lIjowLCJhbGlnbiI6ImxlZnQiLCJsZXR0ZXJzcGFjaW5nIjoibm9ybWFsIiwid29yZHNwYWNpbmciOiJub3JtYWwiLCJ0ZXh0dHJhbnNmb3JtIjoibm9uZSJ9LHsiZXh0cmEiOiIifV19',
            'animation-duration'  => 1000,
            'slider-outer-css'    => '',
            'slider-inner-css'    => 'box-shadow: 0 1px 3px 1px RGBA(0, 0, 0, .3) inset;',
            'slider-title-css'    => 'box-shadow: 0 0 0 1px RGBA(255, 255, 255, .05) inset, 0 0 2px 1px RGBA(0, 0, 0, .3);',
            'animation-easing'    => 'easeOutQuad'
        );
    }

    protected function renderType($css) {

        $params = $this->slider->params;
        N2JS::addStaticGroup(N2Filesystem::translate(dirname(__FILE__)) . '/dist/smartslider-accordion-type-frontend.min.js', 'smartslider-accordion-type-frontend');
    

        $this->jsDependency[] = 'smartslider-accordion-type-frontend';

        echo $this->openSliderElement();
        $this->widgets->echoAbove();

        echo N2Html::openTag('div', array(
            'class' => 'n2-ss-slider-1 n2-ss-swipe-element n2-ow',
        ));

        echo N2Html::openTag('div', array(
            'class' => 'n2-ss-slider-2 n2-ow',
            'style' => $params->get('slider-outer-css')
        ));

        echo N2Html::openTag('div', array(
            'class' => 'n2-ss-slider-3 n2-ow',
            'style' => $params->get('slider-inner-css')
        ));

        echo $this->slider->staticHtml;

        foreach ($this->slider->slides AS $i => $slide) {
            $slide->finalize();

            echo N2Html::openTag('div', $slide->attributes + array(
                    'class' => 'n2-ss-slide n2-ow ' . $slide->classes
                ));
            ?>
            <?php
            $font = $this->slider->addFont($params->get('title-font'), 'accordionslidetitle');

            echo N2Html::openTag('div', array(
                'class' => 'n2-accordion-title n2-ow ' . $font,
                'style' => $params->get('slider-title-css')
            ));
            ?>
            <div class="n2-accordion-title-inner n2-ow">
                    <div class="n2-accordion-title-rotate-90 n2-ow">
                        <?php echo $slide->getTitle(); ?>
                    </div>
                </div>
            <?php echo N2Html::closeTag('div'); ?>

            <?php
            echo N2Html::openTag('div', N2Html::mergeAttributes(array(
                'class' => 'n2-accordion-slide n2-ow',
                'style' => $slide->style
            ), $slide->linkAttributes));
            ?>
            <?php
            echo N2Html::tag('div', array(
                'class' => 'n2-ss-canvas n2-ow',
            ), $slide->background . $slide->getHTML());
            ?>
            </div>
            </div>
            <?php
        }

        echo N2Html::closeTag('div');
        echo N2Html::closeTag('div');
        $this->widgets->echoRemainder();
        echo N2Html::closeTag('div');

        $this->widgets->echoBelow();
        echo N2Html::closeTag('div');

        $this->javaScriptProperties['carousel']      = $params->get('carousel');
        $this->javaScriptProperties['orientation']   = $params->get('orientation');
        $this->javaScriptProperties['mainanimation'] = array(
            'duration' => intval($params->get('animation-duration')),
            'ease'     => $params->get('animation-easing')
        );

        $this->style .= $css->getCSS();

        echo N2Html::clear();
    }

    public function getScript() {
        return "N2R(" . json_encode($this->jsDependency) . ",function(){new N2Classes.SmartSliderAccordion('#{$this->slider->elementId}', " . json_encode($this->javaScriptProperties) . ");});";
    }

    protected function getSliderClasses() {
        return parent::getSliderClasses() . 'n2-accordion-' . $this->slider->params->get('orientation', 'horizontal');
    }
}