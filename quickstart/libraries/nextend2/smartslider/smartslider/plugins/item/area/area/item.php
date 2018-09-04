<?php
N2Loader::import('libraries.renderable.layers.itemFactory', 'smartslider');

class N2SSItemArea extends N2SSItemAbstract {

    protected $type = 'area';

    public function render() {

        return $this->getLink($this->getHtml(), array(
            'style' => 'display: block; width:100%;height:100%;',
            'class' => 'n2-ow'
        ));
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $style = '';

        $color    = $this->data->get('color');
        $gradient = $this->data->get('gradient', 'off');

        if ($gradient != 'off') {
            $colorEnd = $this->data->get('color2');
            switch ($gradient) {
                case 'horizontal':
                    $style .= 'background:#' . substr($color, 0, 6) . ';';
                    $style .= 'background:-moz-linear-gradient(left, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    $style .= 'background:-webkit-linear-gradient(left, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    $style .= 'background:linear-gradient(to right, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    $style .= 'background:filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#' . substr($color, 0, 6) . '\', endColorstr=\'#' . substr($color, 0, 6) . '\',GradientType=1);';
                    break;
                case 'vertical':
                    $style .= 'background:#' . substr($color, 0, 6) . ';';
                    $style .= 'background:-moz-linear-gradient(top, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    $style .= 'background:-webkit-linear-gradient(top, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    $style .= 'background:linear-gradient(to bottom, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    $style .= 'background:filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#' . substr($color, 0, 6) . '\', endColorstr=\'#' . substr($color, 0, 6) . '\',GradientType=0);';
                    break;
                case 'diagonal1':
                    $style .= 'background:#' . substr($color, 0, 6) . ';';
                    $style .= 'background:-moz-linear-gradient(45deg, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    $style .= 'background:-webkit-linear-gradient(45deg, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    $style .= 'background:linear-gradient(45deg, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    $style .= 'background:filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#' . substr($color, 0, 6) . '\', endColorstr=\'#' . substr($color, 0, 6) . '\',GradientType=1);';
                    break;
                case 'diagonal2':
                    $style .= 'background:#' . substr($color, 0, 6) . ';';
                    $style .= 'background:-moz-linear-gradient(-45deg, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    $style .= 'background:-webkit-linear-gradient(-45deg, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    $style .= 'background:linear-gradient(135deg, ' . N2Color::colorToRGBA($color) . ' 0%,' . N2Color::colorToRGBA($colorEnd) . ' 100%);';
                    $style .= 'background:filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#' . substr($color, 0, 6) . '\', endColorstr=\'#' . substr($color, 0, 6) . '\',GradientType=1);';
                    break;
            }
        } else {
            if (strlen($color) == 8 && substr($color, 6, 2) != '00') {
                $style = 'background-color: #' . substr($color, 0, 6) . ';';
                $style .= "background-color: " . N2Color::colorToRGBA($color) . ";";
            }
        }

        $height = '100%';

        $_width = $this->data->get('width');
        if (!empty($_width)) {
            $width = $_width . 'px';
            $style .= 'width:' . $_width . 'px;';
        }

        $_height = $this->data->get('height');
        if (!empty($_height)) {
            $height = $_height . 'px';
        }

        $style .= 'height:' . $height . ';';

        $borderWidth = max(0, intval($this->data->get('borderWidth')));
        if ($borderWidth > 0) {
            list($borderHex, $borderRgba) = N2Color::colorToCss($this->data->get('borderColor'));
            $style .= 'border:' . $borderWidth . 'px solid #' . $borderHex . ';border:' . $borderWidth . 'px solid ' . $borderRgba . ';-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;';
        }
        $borderRadius = max(0, intval($this->data->get('borderRadius')));
        if ($borderRadius > 0) {
            $style .= 'border-radius:' . $borderRadius . 'px;';
        }

        return N2Html::tag('div', array(
            'class' => 'n2-ow',
            'style' => $style . $this->data->get('css')
        ));
    }

    public function needSize() {
        return true;
    }
}
