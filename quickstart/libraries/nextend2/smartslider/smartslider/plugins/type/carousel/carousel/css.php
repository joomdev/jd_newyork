<?php
N2Loader::import('libraries.image.color');

class N2SmartSliderCSSCarousel extends N2SmartSliderCSSAbstract {

    public function __construct($slider) {
        parent::__construct($slider);
        $params = $this->slider->params;

        $width  = intval($this->context['width']);
        $height = intval($this->context['height']);


        $backgroundColor                 = $params->get('background-color');
        $rgba                            = N2Color::hex2rgba($backgroundColor);
        $this->context['backgroundrgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';
        if (substr($backgroundColor, 6, 8) != '00') {
            $this->context['backgroundhex'] = '#' . substr($backgroundColor, 0, 6);
        } else {
            $this->context['backgroundhex'] = 'transparent';
        }

        $this->context['backgroundSize']       = $params->get('background-size');
        $this->context['backgroundAttachment'] = $params->get('background-fixed') ? 'fixed' : 'scroll';


        $backgroundColor                      = $params->get('slide-background-color');
        $rgba                                 = N2Color::hex2rgba($backgroundColor);
        $this->context['slideBackgroundrgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';
        if (substr($backgroundColor, 6, 8) != '00') {
            $this->context['slideBackgroundhex'] = '#' . substr($backgroundColor, 0, 6);
        } else {
            $this->context['slideBackgroundhex'] = 'transparent';
        }

        $this->context['slideBorderRadius'] = $params->get('slide-border-radius') . 'px';

        $borderWidth                   = $params->get('border-width');
        $backgroundColor               = $params->get('border-color');
        $this->context['borderRadius'] = $params->get('border-radius') . 'px';


        $this->context['border'] = $borderWidth . 'px';

        $rgba                        = N2Color::hex2rgba($backgroundColor);
        $this->context['borderrgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';
        $this->context['borderhex']  = '#' . substr($backgroundColor, 0, 6);

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