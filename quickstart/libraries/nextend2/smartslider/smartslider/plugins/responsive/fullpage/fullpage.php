<?php
class N2SSPluginResponsiveFullPage extends N2SSPluginSliderResponsive {

    protected $name = 'fullpage';

    public $ordering = 3;

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
    }

    public function getLabel() {
        return n2_x('Fullpage', 'Slider responsive mode');
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'smartslider-responsive-full-page');

        new N2ElementNumberAutocomplete($settings, 'responsiveSlideWidthMax', n2_('Maximum slide width'), 3000, array(
            'style'  => 'width:40px;',
            'unit'   => 'px',
            'values' => array(
                3000,
                980
            )
        ));

        $focus = new N2ElementGroup($settings, 'slider-focus', n2_('Focus'));
        new N2ElementOnOff($focus, 'responsiveFocusUser', n2_('User interaction'), 0);
        new N2ElementOnOff($focus, 'responsiveFocusAutoplay', n2_('Autoplay'), 0);


        $forceFullWidth = new N2ElementGroup($settings, 'responsive-force-full-width', n2_('Force full width'), array(
            'tip' => n2_('The slider tries to fill the full width of the browser.')
        ));
        new N2ElementOnOff($forceFullWidth, 'responsiveForceFull', n2_('Enable'), 1);
        new N2ElementRadio($forceFullWidth, 'responsiveForceFullOverflowX', n2_('Horizontal mask'), 'body', array(
            'options' => array(
                'body' => 'body',
                'html' => 'html',
                'none' => n2_('None')
            )
        ));

        new N2ElementText($settings, 'responsiveForceFullHorizontalSelector', n2_('Adjust slider width to parent selector'), 'body', array(
            'tip' => n2_('When the jQuery selector of one of the slider\'s parent elements is specified, the slider tries to have the width and fill up that element instead of the window.')
        ));
        new N2ElementOnOff($settings, 'responsiveConstrainRatio', n2_('Constrain ratio'), 0, array(
            'tip' => n2_('The size of the slide proportionately changes together with its layers.')
        ));


        $verticalOffset = new N2ElementGroup($settings, 'vertical-offset', n2_('Modify slider height'));

        if (N2WORDPRESS) {
            $responsiveHeightOffsetValue = '#wpadminbar';
        }//N2WORDPRESS
        if (!N2WORDPRESS) {
            $responsiveHeightOffsetValue = '';
        }//!N2WORDPRESS
        new N2ElementTextAutocomplete($verticalOffset, 'responsiveHeightOffset', n2_('Decrease slider height with the height of the matching elements (CSS selector)'), $responsiveHeightOffsetValue, array(
            'style'  => 'width:400px;',
            'values' => array($responsiveHeightOffsetValue)
        ));
        new N2ElementNumber($verticalOffset, 'responsiveDecreaseSliderHeight', n2_('Decrease slider height'), 0, array(
            'unit' => 'px',
            'wide' => 4,
        ));
    }

    public function parse($params, $responsive, $features) {
        $responsive->scaleDown = 1;
        $responsive->scaleUp   = 1;

        $features->align->align = 'normal';

        $responsive->maximumSlideWidth = intval($params->get('responsiveSlideWidthMax', 3000));

        $responsive->focusUser     = intval($params->get('responsiveFocusUser', 0));
        $responsive->focusAutoplay = intval($params->get('responsiveFocusAutoplay', 0));

        $responsive->forceFull          = intval($params->get('responsiveForceFull', 1));
        $responsive->forceFullOverflowX = $params->get('responsiveForceFullOverflowX', 'body');

        $responsive->forceFullHorizontalSelector    = $params->get('responsiveForceFullHorizontalSelector', 'body');
        $responsive->constrainRatio                 = intval($params->get('responsiveConstrainRatio', 0));
        $responsive->verticalOffsetSelectors        = $params->get('responsiveHeightOffset', '#wpadminbar');
        $responsive->responsiveDecreaseSliderHeight = intval($params->get('responsiveDecreaseSliderHeight', 0));
    }
}

N2SSPluginSliderResponsive::addType(new N2SSPluginResponsiveFullPage);
