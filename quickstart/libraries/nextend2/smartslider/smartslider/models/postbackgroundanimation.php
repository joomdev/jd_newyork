<?php
N2Loader::import(array(
    'libraries.postbackgroundanimation.storage'
), 'smartslider');

class N2SmartSliderPostBackgroundAnimationModel extends N2SystemVisualModel {

    public $type = 'postbackgroundanimation';

    public function __construct($tableName = null) {

        parent::__construct($tableName);
        $this->storage = N2Base::getApplication('smartslider')->storage;
    }

    protected function getPath() {
        return dirname(__FILE__);
    }

    public function renderSetsForm() {

        $form    = new N2Form();
        $setsTab = new N2TabNaked($form, 'postbackgroundanimation-sets');
        new N2ElementList($setsTab, 'sets', '', '');

        echo $form->render($this->type . 'set');
    }

    public function renderForm() {
        $form = new N2Form();

        $properties = new N2Tab($form, 'ken-burns-form', n2_('Properties'), array(
            'class' => 'n2-expert'
        ));

        $transformOrigin = new N2ElementMixed($properties, 'transformorigin', n2_('Focus'), '50|*|50');

        new N2ElementNumberAutocomplete($transformOrigin, 'transformorigin-1', false, '', array(
            'style'    => 'width:22px;',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%',
            'sublabel' => 'X'
        ));

        new N2ElementNumberAutocomplete($transformOrigin, 'transformorigin-2', false, '', array(
            'style'    => 'width:22px;',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%',
            'sublabel' => 'Y'
        ));

        $form->render('n2-post-background');
    }
}
