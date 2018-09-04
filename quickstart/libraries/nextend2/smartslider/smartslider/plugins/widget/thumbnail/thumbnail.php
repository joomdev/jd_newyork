<?php

class N2SSPluginWidgetThumbnail extends N2SSPluginSliderWidget {

    public $ordering = 6;

    protected $name = 'thumbnail';

    public function getLabel() {
        return n2_('Thumbnails');
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
    }

    public function renderFields($form) {
        $settings = new N2Tab($form, 'widgetsthumbnail');

        $url = N2Base::getApplication('smartslider')
                     ->getApplicationType('backend')->router->createAjaxUrl(array("slider/renderwidgetthumbnail"));

        new N2ElementWidgetPluginMatrix($settings, 'widgetthumbnail', false, '', $url, array(
            'widget' => $this
        ));
        $display = new N2ElementGroup($settings, 'widget-thumbnail-display', n2_('Display'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementOnOff($display, 'widget-thumbnail-display-desktop', n2_('Desktop'), 1);
        new N2ElementOnOff($display, 'widget-thumbnail-display-tablet', n2_('Tablet'), 1);
        new N2ElementOnOff($display, 'widget-thumbnail-display-mobile', n2_('Mobile'), 1);
        new N2ElementText($display, 'widget-thumbnail-exclude-slides', n2_('Hide on slides'), '', array(
            'tip' => n2_('List the slides separated by commas on which you want the controls to be hidden.')
        ));

    

        new N2ElementOnOff($settings, 'widget-thumbnail-display-hover', n2_('Shows on hover'), 0);


        $thumbnail = new N2elementGroup($settings, 'thumbnail-thumbnail', n2_('Thumbnail'));
        new N2ElementOnOff($thumbnail, 'widget-thumbnail-show-image', n2_('Enable'), 1, array(
            'rowClass'      => 'n2-expert',
            'relatedFields' => array(
                'widget-thumbnail-width',
                'widget-thumbnail-height'
            )
        ));
    

        new N2ElementNumberAutocomplete($thumbnail, 'widget-thumbnail-width', n2_('Width'), 100, array(
            'unit'   => 'px',
            'values' => array(
                60,
                100,
                150,
                200
            ),
            'style'  => 'width:30px'
        ));

        new N2ElementNumberAutocomplete($thumbnail, 'widget-thumbnail-height', n2_('Height'), 60, array(
            'unit'   => 'px',
            'values' => array(
                60,
                100,
                150,
                200
            ),
            'style'  => 'width:30px'
        ));


        new N2TabPlaceholder($form, 'widget-thumbnail-placeholder', false, array(
            'id' => 'nextend-widgetthumbnail-panel'
        ));

    }
}

N2SmartSliderWidgets::addGroup(new N2SSPluginWidgetThumbnail);