<?php

class N2SmartSliderBackendSplitTextAnimationControllerAjax extends N2SystemBackendVisualManagerControllerAjax {

    protected $type = 'splittextanimation';

    public function initialize() {
        parent::initialize();

        N2Loader::import(array(
            'models.' . $this->type
        ), 'smartslider');
    }

    public function getModel() {
        return new N2SmartSliderSplitTextAnimationModel();
    }
}
