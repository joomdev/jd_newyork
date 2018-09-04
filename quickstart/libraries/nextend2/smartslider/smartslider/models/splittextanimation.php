<?php
N2Loader::import(array(
    'libraries.splittextanimation.storage'
), 'smartslider');

class N2SmartSliderSplitTextAnimationModel extends N2SystemVisualModel {

    public $type = 'splittextanimation';

    public function __construct($tableName = null) {

        parent::__construct($tableName);
        $this->storage = N2Base::getApplication('smartslider')->storage;
    }

    public function renderSetsForm() {

        $form    = new N2Form();
        $setsTab = new N2TabNaked($form, 'splittextanimation-sets', n2_('Split text sets'));
        new N2ElementList($setsTab, 'sets', '', '');

        echo $form->render($this->type . 'set');
    }

    public function renderForm() {
        $form     = new N2Form();
        $firstRow = new N2TabHorizontal($form, 'firstrow');

        new N2ElementRadio($firstRow, 'mode', n2_('Mode'), 'chars', array(
            'options' => array(
                'chars' => n2_('Chars'),
                'words' => n2_('Words'),
                'lines' => n2_('Lines')
            )
        ));

        new N2ElementList($firstRow, 'sort', n2_('Sort'), 'normal', array(
            'options' => array(
                'normal'        => n2_('Normal'),
                'reversed'      => n2_('Reversed'),
                'random'        => n2_('Random'),
                'side'          => n2_('Side'),
                'sideShifted'   => n2_('Side shifted'),
                'center'        => n2_('Center'),
                'centerShifted' => n2_('Center shifted')
            )
        ));

        new N2ElementNumberAutocomplete($firstRow, 'duration', n2_('Duration'), 800, array(
            'style'  => 'width:40px;',
            'min'    => 0,
            'values' => array(
                500,
                800,
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms'
        ));

        new N2ElementNumberAutocomplete($firstRow, 'stagger', n2_('Stagger'), 50, array(
            'style'  => 'width:40px;',
            'values' => array(
                25,
                50,
                100,
                200,
                400
            ),
            'unit'   => 'ms'
        ));

        new N2ElementEasing($firstRow, 'easing', n2_('Easing'), 'easeOutCubic');

        new N2ElementNumberAutocomplete($firstRow, 'opacity', n2_('Opacity'), 100, array(
            'style'  => 'width:22px;',
            'min'    => 0,
            'max'    => 100,
            'values' => array(
                0,
                100
            ),
            'unit'   => '%'
        ));

        new N2ElementNumberAutocomplete($firstRow, 'scale', n2_('Scale'), 100, array(
            'style'  => 'width:40px;',
            'min'    => 0,
            'max'    => 9999,
            'values' => array(
                0,
                50,
                100,
                150,
                1000
            ),
            'unit'   => '%'
        ));

        $offset = new N2ElementMixed($firstRow, 'offset', n2_('Offset'), '0|*|0');

        new N2ElementNumberAutocomplete($offset, 'offset-1', false, '', array(
            'style'    => 'width:40px;',
            'sublabel' => 'X',
            'values'   => array(
                -400,
                -200,
                -100,
                0,
                100,
                200,
                400
            ),
            'unit'     => 'px'
        ));

        new N2ElementNumberAutocomplete($offset, 'offset-2', false, '', array(
            'style'    => 'width:40px;',
            'sublabel' => 'Y',
            'values'   => array(
                -400,
                -200,
                -100,
                0,
                100,
                200,
                400
            ),
            'unit'     => 'px'
        ));

        $rotate = new N2ElementMixed($firstRow, 'rotate', n2_('Rotate'), '0|*|0|*|0');

        new N2ElementNumberAutocomplete($rotate, 'rotate-1', false, '', array(
            'style'    => 'width:40px;',
            'sublabel' => 'X',
            'values'   => array(
                0,
                90,
                180,
                -90,
                -180
            ),
            'unit'     => '°'
        ));

        new N2ElementNumberAutocomplete($rotate, 'rotate-2', false, '', array(
            'style'    => 'width:40px;',
            'sublabel' => 'Y',
            'values'   => array(
                0,
                90,
                180,
                -90,
                -180
            ),
            'unit'     => '°'
        ));

        new N2ElementNumberAutocomplete($rotate, 'rotate-3', false, '', array(
            'style'    => 'width:40px;',
            'sublabel' => 'Z',
            'values'   => array(
                0,
                90,
                180,
                -90,
                -180
            ),
            'unit'     => '°'
        ));

        $form->render('n2-splittextanimation-editor');
    }

    public function renderFormExtra() {
        $form = new N2Form();

        $global = new N2Tab($form, 'splittextglobal', n2_('Common split text properties'), array(
            'class' => 'n2-expert'
        ));


        $transformOrigin = new N2ElementMixed($global, 'transformorigin', n2_('Transform origin'), '50|*|50|*|0');

        new N2ElementNumberAutocomplete($transformOrigin, 'transformorigin-1', false, '', array(
            'style'    => 'width:40px;',
            'sublabel' => 'X',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%'
        ));

        new N2ElementNumberAutocomplete($transformOrigin, 'transformorigin-2', false, '', array(
            'style'    => 'width:40px;',
            'sublabel' => 'Y',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%'
        ));

        new N2ElementNumberAutocomplete($transformOrigin, 'transformorigin-3', false, '', array(
            'style'    => 'width:40px;',
            'sublabel' => 'Z',
            'values'   => array(
                0
            ),
            'unit'     => 'px'
        ));

        $form->render('n2-splittextanimation-editor');
    }

    protected function getPath() {
        return dirname(__FILE__);
    }
}
