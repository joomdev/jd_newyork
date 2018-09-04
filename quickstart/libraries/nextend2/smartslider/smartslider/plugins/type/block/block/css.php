<?php

class N2SmartSliderCSSBlock extends N2SmartSliderCSSAbstract {

    public function __construct($slider) {
        parent::__construct($slider);
        $params = $this->slider->params;
        N2Loader::import('libraries.image.color');

        $width  = intval($this->context['width']);
        $height = intval($this->context['height']);

        $this->context['backgroundSize']       = $params->get('background-size');
        $this->context['backgroundAttachment'] = $params->get('background-fixed') ? 'fixed' : 'scroll';

        $this->context['canvaswidth']  = $width . "px";
        $this->context['canvasheight'] = $height . "px";

        $this->initSizes();

        $this->slider->addLess(N2Filesystem::translate(dirname(__FILE__) . NDS . 'style.n2less'), $this->context);
    }

}