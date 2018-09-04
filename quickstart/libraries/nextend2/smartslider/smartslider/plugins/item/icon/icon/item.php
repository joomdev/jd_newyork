<?php

N2Loader::import('libraries.renderable.layers.itemFactory', 'smartslider');

class N2SSItemIcon extends N2SSItemAbstract {

    protected $type = 'icon';

    public function render() {
        return $this->getLink($this->getHtml(), array(
            'style' => 'display:block;',
            'class' => 'n2-ow'
        ));
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $svg = $this->data->get('icon', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="32" height="32"><rect width="100" height="100" data-style="{style}" /></svg>');


        list($width, $height) = (array)N2Parse::parse($this->data->get('size', '100%|*|auto'));
        list($color, $alpha) = N2Color::colorToSVG($this->data->get('color', '00000080'));
        $style = 'fill:#' . $color . ';fill-opacity:' . $alpha;

        list($colorHover, $alphaHover) = N2Color::colorToSVG($this->data->get('color-hover', '00000000'));
        $styleHover = 'fill:#' . $colorHover . ';fill-opacity:' . $alphaHover;

        $styleClass = $owner->addStyle($this->data->get('style'), 'heading');

        if ($alphaHover != 0) {
            $styleClass .= ' n2-ss-icon-has-hover';
        }

        return '<span class="' . $styleClass . ' n2-ow" style="display:block;">' . N2Html::image('data:image/svg+xml;base64,' . n2_base64_encode(str_replace(array(
                    'data-style',
                    '{style}'
                ), array(
                    'style',
                    $style
                ), $svg)), 'Icon', array(
                'class' => 'n2-ow n2-ss-icon-normal',
                'style' => 'width:' . $width . ';height:' . $height . ';'
            )) . ($alphaHover == 0 ? '' : N2Html::image('data:image/svg+xml;base64,' . n2_base64_encode(str_replace(array(
                    'data-style',
                    '{style}'
                ), array(
                    'style',
                    $styleHover
                ), $svg)), 'Icon', array(
                'class' => 'n2-ow n2-ss-icon-hover',
                'style' => 'width:' . $width . ';height:' . $height . ';'
            ))) . '</span>';
    }
}