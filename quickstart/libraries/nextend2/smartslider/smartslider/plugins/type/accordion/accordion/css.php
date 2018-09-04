<?php
N2Loader::import('libraries.image.color');
N2Loader::import('libraries.parse.font');

class N2SmartSliderCSSAccordion extends N2SmartSliderCSSAbstract {

    public function __construct($slider) {
        parent::__construct($slider);
        $params = $this->slider->params;

        $orientation = $params->get('orientation');

        $width  = intval($this->context['width']);
        $height = intval($this->context['height']);


        $border1                  = $params->get('outer-border');
        $this->context['border1'] = $border1 . 'px';

        $borderOuterColor             = $params->get('outer-border-color');
        $rgba                         = N2Color::hex2rgba($borderOuterColor);
        $this->context['border1rgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';
        $this->context['border1hex']  = '#' . substr($borderOuterColor, 0, 6);

        $border2                  = $params->get('inner-border');
        $this->context['border2'] = $border2 . 'px';

        $borderInnerColor             = $params->get('inner-border-color');
        $rgba                         = N2Color::hex2rgba($borderInnerColor);
        $this->context['border2rgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';
        $this->context['border2hex']  = '#' . substr($borderInnerColor, 0, 6);

        $this->context['borderRadius'] = intval($params->get('border-radius')) . 'px';

        $orientationMargin                  = intval($params->get('title-margin'));
        $this->context['orientationmargin'] = $orientationMargin . 'px';

        $this->context['tabbg']       = '#' . $params->get('tab-normal-color');
        $this->context['tabbgactive'] = '#' . $params->get('tab-active-color');

        $slideMargin = max(0, $params->get('slide-margin'));

        $this->context['slidemargin']    = $slideMargin . 'px';
        $this->context['slidemarginneg'] = -$slideMargin . 'px';


        $title      = max(10, $params->get('title-size'));
        $titleSizes = $title * $this->context['count'];

        $this->context['titleBorderRadius'] = max(0, intval($params->get('title-border-radius'))) . 'px';

        $this->context['inner1margin'] = '0';

        if ($this->context['canvas']) {

            switch ($orientation) {
                case 'vertical':
                    $width  += 2 * ($border1 + $border2) + $slideMargin * 2;
                    $height += 2 * ($border1 + $border2 + $slideMargin * $this->context['count']) + $titleSizes;
                    break;
                default:
                    $width  += 2 * ($border1 + $border2) + $slideMargin * 2 * $this->context['count'] + $titleSizes;
                    $height += 2 * ($border1 + $border2 + $slideMargin);
            }

            $this->context['width']  = $width . "px";
            $this->context['height'] = $height . "px";
        }

        $width                          = $width - 2 * $border1;
        $height                         = $height - 2 * $border1;
        $this->context['border1width']  = $width . 'px';
        $this->context['border1height'] = $height . 'px';

        $width                          = $width - 2 * $border2;
        $height                         = $height - 2 * $border2;
        $this->context['border2width']  = $width . 'px';
        $this->context['border2height'] = $height . 'px';


        switch ($orientation) {
            case 'vertical':
                $width  = $width - 2 * $slideMargin;
                $height = $height - 2 * $this->context['count'] * $slideMargin;

                $this->context['titlewidth']   = $width . "px";
                $this->context['titleheight']  = $title . "px";
                $this->context['canvaswidth']  = $width . "px";
                $this->context['canvasheight'] = $height - $titleSizes . "px";
                break;
            default:
                $width  = $width - 2 * ($this->context['count']) * $slideMargin;
                $height = $height - 2 * $slideMargin;

                $this->context['titlewidth']   = $title . "px";
                $this->context['titleheight']  = $height . "px";
                $this->context['canvaswidth']  = $width - $titleSizes . "px";
                $this->context['canvasheight'] = $height . "px";
        }

        $this->initSizes();

        if ($orientation == 'vertical') {
            $this->slider->addLess(N2Filesystem::translate(dirname(__FILE__) . NDS . 'vertical.n2less'), $this->context);
        } else {
            $this->slider->addLess(N2Filesystem::translate(dirname(__FILE__) . NDS . 'horizontal.n2less'), $this->context);
        }
    }
}