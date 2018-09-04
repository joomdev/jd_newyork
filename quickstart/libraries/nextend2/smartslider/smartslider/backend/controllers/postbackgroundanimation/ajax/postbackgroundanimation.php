<?php
N2Base::getApplication('system')
      ->getApplicationType('backend');

class N2SmartSliderBackendPostBackgroundAnimationControllerAjax extends N2SystemBackendVisualManagerControllerAjax
{

    protected $type = 'postbackgroundanimation';

    public function initialize() {
        parent::initialize();

        N2Loader::import(array(
            'models.' . $this->type
        ), 'smartslider');
    }

    public function getModel() {
        return new N2SmartSliderPostBackgroundAnimationModel();
    }
}
