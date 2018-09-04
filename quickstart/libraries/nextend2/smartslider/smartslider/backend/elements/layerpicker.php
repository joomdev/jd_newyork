<?php
N2Loader::import('libraries.form.elements.hidden');

class N2ElementLayerPicker extends N2ElementHidden {

    public $hasTooltip = true;

    protected function fetchElement() {

        N2JS::addInline('new N2Classes.FormElementLayerPicker("' . $this->fieldID . '");');

        return parent::fetchElement() . N2Html::tag('div', array('class' => 'n2-ss-layer-picker'), '<i class="n2-i n2-it n2-i-16 n2-i-layerunlink"></i>');
    }
}
