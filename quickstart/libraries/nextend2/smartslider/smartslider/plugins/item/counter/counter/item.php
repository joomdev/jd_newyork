<?php
N2Loader::import('libraries.renderable.layers.itemFactory', 'smartslider');

class N2SSItemCounter extends N2SSItemAbstract {

    protected $type = 'counter';

    public function render() {
        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $value      = intval($this->data->get('value'));
        $min        = min(0, $value);
        $startvalue = max(intval($this->data->get('startvalue')), $min);
        $total      = max($startvalue, $value);
        $duration   = max(0, intval($this->data->get('animationduration')));

        $fromPercent = (min($startvalue, $total) - $min) / ($total - $min);
        $toPercent   = (min($value, $total) - $min) / ($total - $min);
        if ($duration == 0) {
            // We do not have animation
            $fromPercent = $toPercent;
        }

        $label     = $this->data->get('label');
        $placement = '';
        if (!empty($label)) {

            $fontLabel = $owner->addFont($this->data->get('fontlabel'), 'simple', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');

            $labelHTML = N2Html::tag('div', array(
                'class' => $fontLabel
            ), $label);
            $placement = $this->data->get('labelplacement');
        }

        $html = '';

        if ($placement == 'before') {
            $html .= $labelHTML;
        }

        $font = $owner->addFont($this->data->get('font'), 'simple', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');

        $pre             = $this->data->get('pre');
        $post            = $this->data->get('post');
        $countingDivHTML = N2Html::tag('div', array(
            'class' => 'n2-ss-item-counter-counting-div n2-ow ' . $font
        ), $pre . round($min + $fromPercent * ($total - $min)) . $post);

        $html .= N2Html::tag('div', array(
            'id'    => $this->id,
            'class' => 'n2-ow'
        ), $countingDivHTML);


        if ($placement == 'after') {
            $html .= $labelHTML;
        }

        if ($duration != 0) {

            $jsData = array(
                'name'        => 'counter',
                'pre'         => $pre,
                'post'        => $post,
                'fromPercent' => $fromPercent,
                'toPercent'   => $toPercent,
                'duration'    => $duration,
                'delay'       => $this->data->get('animationdelay'),
                'min'         => $min,
                'total'       => $total,
                'counting'    => '.n2-ss-item-counter-counting-div',
                'displayMode' => false
            );

            if ($this->isEditor && $owner->underEdit) {
                $owner->addScript('new N2Classes.CounterItemAdmin(this, "#' . $this->id . '", ' . json_encode($jsData) . ');');
            } else {
                $owner->addScript('new N2Classes.FrontendItemCounter(this, "#' . $this->id . '", ' . json_encode($jsData) . ');');
            }
        }

        return $html;
    }
}
