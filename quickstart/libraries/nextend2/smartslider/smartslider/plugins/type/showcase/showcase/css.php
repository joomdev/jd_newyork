<?php
N2Loader::import('libraries.image.color');

class N2SmartSliderCSSShowcase extends N2SmartSliderCSSAbstract {

    public function __construct($slider) {
        parent::__construct($slider);
        $params = $this->slider->params;


        $slideDistance = intval($params->get('slide-distance'));
        if ($slideDistance == 0) {
            $slideDistance = -1;
        }

        switch ($params->get('animation-direction')) {
            case 'vertical':
                $this->context['distanceh'] = 0;
                $this->context['distancev'] = intval($params->get('slide-distance')) . 'px';
                break;
            default:
                $this->context['distancev'] = 0;
                $this->context['distanceh'] = intval($params->get('slide-distance')) . 'px';
        }


        $this->context['perspective'] = intval($params->get('perspective')) . 'px';


        $width  = intval($this->context['width']);
        $height = intval($this->context['height']);

        $this->context['backgroundSize']       = $params->get('background-size');
        $this->context['backgroundAttachment'] = $params->get('background-fixed') ? 'fixed' : 'scroll';

        $borderWidth                   = $params->get('border-width');
        $borderColor                   = $params->get('border-color');
        $this->context['borderRadius'] = $params->get('border-radius') . 'px';


        $this->context['border'] = $borderWidth . 'px';

        $rgba                        = N2Color::hex2rgba($borderColor);
        $this->context['borderrgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';
        $this->context['borderhex']  = '#' . substr($borderColor, 0, 6);

        $width                         = $width - $borderWidth * 2;
        $height                        = $height - $borderWidth * 2;
        $this->context['inner1height'] = $height . 'px';

        $this->context['fullcanvaswidth']  = $width . 'px';
        $this->context['fullcanvasheight'] = $height . 'px';

        $this->context['canvaswidth']  = min($width, max(50, intval($params->get('slide-width')))) . 'px';
        $this->context['canvasheight'] = min($height, max(50, intval($params->get('slide-height')))) . 'px';

        $this->initSizes();

        $this->slider->addLess(N2Filesystem::translate(dirname(__FILE__) . NDS . 'style.n2less'), $this->context);
    }
}