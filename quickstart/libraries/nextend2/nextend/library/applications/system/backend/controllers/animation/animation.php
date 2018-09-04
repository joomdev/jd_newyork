<?php

class N2SystemBackendAnimationController extends N2SystemBackendVisualManagerController
{

    protected $type = 'animation';

    public function __construct($path, $appType, $defaultParams) {
        $this->logoText = n2_('Animation');

        N2Localization::addJS(array(
            'animation',
            'animations',
        ));

        parent::__construct($path, $appType, $defaultParams);
    }

    public function getModel() {
        return new N2SystemAnimationModel();
    }
}