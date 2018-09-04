<?php

N2Loader::import('libraries.splittextanimation.manager', 'smartslider');

class N2ElementSplitTextAnimation extends N2ElementHidden {

    protected $hasTooltip = true;

    protected $relatedStyle = '';
    protected $relatedFont = '';
    protected $group = '';
    protected $transformOrigin = '';
    protected $preview = '';


    protected function fetchElement() {

        N2JS::addInline('new N2Classes.FormElementSplitTextAnimationManager("' . $this->fieldID . '", {
        font: "' . $this->relatedFont . '",
        style: "' . $this->relatedStyle . '",
        preview: ' . json_encode($this->preview) . ',
        group: "' . $this->group . '",
        transformOrigin: "' . $this->transformOrigin . '"
    });');

        return N2Html::tag('div', array(
            'class' => 'n2-form-element-option-chooser n2-border-radius'
        ), parent::fetchElement() . N2Html::tag('input', array(
                'type'     => 'text',
                'class'    => 'n2-h5',
                'style'    => $this->style,
                'disabled' => 'disabled'
            ), false) . N2Html::tag('a', array(
                'href'  => '#',
                'class' => 'n2-form-element-clear'
            ), N2Html::tag('i', array('class' => 'n2-i n2-it n2-i-empty n2-i-grey-opacity'), '')) . N2Html::tag('a', array(
                'href'  => '#',
                'class' => 'n2-form-element-button n2-icon-button n2-h5 n2-uc'
            ), '<i class="n2-i n2-it n2-i-animation"></i>'));
    }

    /**
     * @param string $relatedStyle
     */
    public function setRelatedStyle($relatedStyle) {
        $this->relatedStyle = $relatedStyle;
    }

    /**
     * @param string $relatedFont
     */
    public function setRelatedFont($relatedFont) {
        $this->relatedFont = $relatedFont;
    }

    /**
     * @param string $group
     */
    public function setGroup($group) {
        $this->group = $group;
    }

    /**
     * @param string $transformOrigin
     */
    public function setTransformOrigin($transformOrigin) {
        $this->transformOrigin = $transformOrigin;
    }

    /**
     * @param string $preview
     */
    public function setPreview($preview) {
        $this->preview = $preview;
    }

}
