<?php
N2Loader::import('libraries.renderable.layers.itemFactory', 'smartslider');

class N2SSItemInput extends N2SSItemAbstract {

    protected $type = 'input';

    public function render() {
        $owner = $this->layer->getOwner();

        $style = $owner->addStyle($this->data->get('style'), 'heading');

        $inputFont  = $owner->addFont($this->data->get('inputfont'), 'paragraph', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');
        $inputStyle = $owner->addStyle($this->data->get('inputstyle'), 'heading', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');

        $slideSubmitAction = $this->data->get('submit');
        if (!empty($slideSubmitAction)) {
            $owner->addScript('$("#' . $this->id . '").closest(".n2-ss-slide").on("' . $this->data->get('submit') . '", function(e){$("#' . $this->id . '").trigger("submit")})');
        }

        $parameters     = explode('&', $owner->fill($this->data->get('parameters')));
        $parametersHTML = '';
        foreach ($parameters AS $parameter) {
            $parameter = explode('=', $parameter);
            if (count($parameter) == 2) {
                $parametersHTML .= N2Html::tag('input', array(
                    'type'  => 'hidden',
                    'name'  => $parameter[0],
                    'value' => $parameter[1],
                    'class' => 'n2-ow'
                ), false);
            }
        }


        $button      = '';
        $buttonLabel = $owner->fill($this->data->get('buttonlabel'));
        if (!empty($buttonLabel)) {

            $buttonFont  = $owner->addFont($this->data->get('buttonfont'), 'hover', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');
            $buttonStyle = $owner->addStyle($this->data->get('buttonstyle'), 'heading', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');

            $button = N2Html::tag('input', array(
                'style' => 'white-space:nowrap;',
                'type'  => 'submit',
                'value' => $buttonLabel,
                'class' => 'n2-form-button ' . $buttonFont . ' ' . $buttonStyle . ' n2-ow'
            ), false);
        }


        return N2Html::tag('form', array(
            'class'    => 'n2-ss-item-input-form ' . $style . ' n2-ow ' . $owner->fill($this->data->get('class', '')),
            'id'       => $this->id,
            'action'   => $owner->fill($this->data->get('action')),
            'method'   => $this->data->get('method'),
            'target'   => $this->data->get('target'),
            'onsubmit' => $this->data->get('onsubmit')
        ), N2Html::tag('input', array(
                'name'        => $owner->fill($this->data->get('name', '')),
                'type'        => 'text',
                'placeholder' => strip_tags($owner->fill($this->data->get('placeholder', ''))),
                'class'       => 'n2-input n2-ow ' . $inputFont . $inputStyle,
                'style'       => 'display: block; width: 100%;white-space:nowrap;',
                'onkeyup'     => $this->data->get('onkeyup')
            ), false) . $parametersHTML . $button);
    }

    public function _renderAdmin() {
        $owner = $this->layer->getOwner();

        $style = $owner->addStyle($this->data->get('style'), 'heading');


        $inputFont  = $owner->addFont($this->data->get('inputfont'), 'paragraph', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');
        $inputStyle = $owner->addStyle($this->data->get('inputstyle'), 'heading', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');

        $button      = '';
        $buttonLabel = $owner->fill($this->data->get('buttonlabel'));
        if (!empty($buttonLabel)) {
            $buttonFont  = $owner->addFont($this->data->get('buttonfont'), 'hover', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');
            $buttonStyle = $owner->addStyle($this->data->get('buttonstyle'), 'heading', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');

            $button = N2Html::tag('div', array(
                'style' => 'white-space:nowrap;',
                'class' => 'n2-form-button ' . $buttonFont . ' ' . $buttonStyle . ' n2-ow'
            ), $buttonLabel);
        }


        return N2Html::tag('div', array(
            'class' => 'n2-ss-item-input-form ' . $style . ' ' . $this->data->get('class', '') . ' n2-ow'
        ), "<div class='n2-input n2-ow " . $inputFont . " " . $inputStyle . "' style='white-space:nowrap;'>" . strip_tags($owner->fill($this->data->get('placeholder', ''))) . "</div>" . $button);

    }
}
