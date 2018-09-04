<?php
N2Loader::import('libraries.renderable.layers.itemFactory', 'smartslider');
N2Loader::import('libraries.image.color');

class N2SSItemProgressBar extends N2SSItemAbstract {

    protected $type = 'progressbar';

    public function render() {
        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $this->loadResources($owner);

        $value      = intval($this->data->get('value'));
        $min        = min(0, $value);
        $startvalue = max(intval($this->data->get('startvalue')), $min);
        $total      = max(max(intval($this->data->get('total')), $startvalue), $value);
        $duration   = max(0, intval($this->data->get('animationduration')));

        $fromPercent = (min($startvalue, $total) - $min) / ($total - $min);
        $toPercent   = (min($value, $total) - $min) / ($total - $min);
        if ($duration == 0) {
            // We do not have animation
            $fromPercent = $toPercent;
        }


        $labelHTML = '';
        $label     = $this->data->get('label');
        $placement = '';
        if (!empty($label)) {
            $fontLabel = $owner->addFont($this->data->get('fontlabel'), 'simple', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');

            $labelHTML = N2Html::tag('div', array(
                'class' => 'n2-ss-item-progressbar-label n2-ow ' . $fontLabel
            ), $label);
            $placement = $this->data->get('labelplacement');
        }

        $html = '';

        if ($placement == 'before') {
            $html .= $labelHTML;
        }

        $html .= N2Html::openTag('div', array(
            'id'    => $this->id,
            'class' => 'n2-ow n2-ss-item-progressbar',
            'style' => 'background-color: ' . N2Color::colorToRGBA($this->data->get('color')) . ';'
        ));

        $html .= N2Html::openTag('div', array(
            'class' => 'n2-ow n2-ss-item-progressbar-inner',
            'style' => 'width:' . $fromPercent * 100 . '%;background-color: ' . N2Color::colorToRGBA($this->data->get('color2')) . ';'
        ));

        if ($placement == 'over') {
            $html .= $labelHTML;
        }


        $font = $owner->addFont($this->data->get('font'), 'simple', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');

        $pre  = $this->data->get('pre');
        $post = $this->data->get('post');

        $html .= N2Html::tag('div', array(
            'class' => 'n2-ss-item-progressbar-counting n2-ow ' . $font
        ), $pre . round($min + $fromPercent * ($total - $min)) . $post);


        $html .= '</div>';

        $html .= '</div>';

        if ($placement == 'after') {
            $html .= $labelHTML;
        }

        if ($duration != 0) {

            $jsData = array(
                'name'        => 'progressbar',
                'pre'         => $pre,
                'post'        => $post,
                'fromPercent' => $fromPercent,
                'toPercent'   => $toPercent,
                'duration'    => $duration,
                'delay'       => $this->data->get('animationdelay'),
                'min'         => $min,
                'total'       => $total,
                'counting'    => '.n2-ss-item-progressbar-counting',
                'display'     => '.n2-ss-item-progressbar-inner',
                'displayMode' => 'width'
            );

            if ($this->isEditor && $owner->underEdit) {
                $owner->addScript('new N2Classes.CounterItemAdmin(this, "#' . $this->id . '", ' . json_encode($jsData) . ');');
            } else {
                $owner->addScript('new N2Classes.FrontendItemCounter(this, "#' . $this->id . '", ' . json_encode($jsData) . ');');
            }
        }

        return $html;
    }

    /**
     * @param $owner N2SmartSliderComponentOwnerAbstract
     */
    public function loadResources($owner) {
        $owner->addLess(dirname(__FILE__) . "/progressbar.n2less", array(
            "sliderid" => $owner->getElementID()
        ));
    }
}
