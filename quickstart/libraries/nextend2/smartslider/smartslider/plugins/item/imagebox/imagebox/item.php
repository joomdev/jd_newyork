<?php
N2Loader::import('libraries.renderable.layers.itemFactory', 'smartslider');
N2Loader::import('libraries.icons.icons');

class N2SSItemImageBox extends N2SSItemAbstract {

    protected $type = 'imagebox';

    public function render() {
        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $this->loadResources($owner);

        $style = $owner->addStyle($this->data->get('style'), 'heading');

        $layout = $this->data->get('layout');

        $attr = array(
            'class'             => 'n2-ss-item-imagebox-container n2-ow-all ' . $style,
            'data-layout'       => $layout,
            'data-csstextalign' => $this->data->get('inneralign')
        );
        if ($layout == 'left' || $layout == 'right') {
            $attr['data-verticalalign'] = $this->data->get('verticalalign');
        }

        $html = N2HTML::openTag('div', $attr);

        // START IMAGE SECTION
        $imageHTML           = '';
        $imageContainerStyle = '';
        $icon                = $this->data->get('icon');
        $image               = $this->data->get('image');
        if (!empty($icon)) {
            $iconData  = N2Icons::render($icon);
            $imageHTML .= N2HTML::tag('i', array(
                'class' => 'n2i ' . $iconData['class'],
                'style' => 'color: ' . N2Color::colorToRGBA($this->data->get('iconcolor')) . ';font-size:' . ($this->data->get('iconsize') / 16 * 100) . '%'
            ), $iconData['ligature']);
        } else if (!empty($image)) {

            if ($layout == 'top' || $layout == 'bottom') {
                $imageContainerStyle .= 'width:' . $this->data->get('imagewidth') . '%;';
            } else {
                $imageContainerStyle .= 'max-width:' . $this->data->get('imagewidth') . '%;';
            }

            $fixedImageUrl = N2ImageHelper::fixed($owner->fill($this->data->get('image')));

            $owner->addImage($fixedImageUrl);

            $imageHTML .= N2HTML::image($fixedImageUrl, $owner->fill($this->data->get('imagealt')));
        }

        if (!empty($imageHTML)) {
            $html .= N2HTML::tag('div', array(
                'class' => 'n2-ss-item-imagebox-image n2-ow',
                'style' => $imageContainerStyle
            ), $this->getLink($imageHTML));
        }
        // END IMAGE SECTION


        // START CONTENT SECTION
        $html .= N2HTML::openTag('div', array(
            'class' => 'n2-ss-item-imagebox-content n2-ow',
            'style' => 'padding:' . implode('px ', explode('|*|', $this->data->get('padding'))) . 'px'
        ));

        $heading = $this->data->get('heading');
        if (!empty($heading)) {
            $font = $owner->addFont($this->data->get('fonttitle'), 'hover', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');

            $html .= $this->getLink(N2HTML::tag($this->data->get('headingpriority'), array('class' => $font), $owner->fill($heading)));
        }

        $description = $this->data->get('description');
        if (!empty($description)) {
            $font = $owner->addFont($this->data->get('fontdescription'), 'paragraph', 'div#' . $owner->getElementID() . ' .n2-ss-layer ');

            $html .= N2HTML::tag('div', array('class' => $font), $owner->fill($description));
        }

        $html .= '</div>';
        // END CONTENT SECTION


        $html .= '</div>';

        return $html;
    }

    /**
     * @param N2SmartSliderComponentOwnerAbstract $owner
     */
    public function loadResources($owner) {
        $owner->addLess(dirname(__FILE__) . "/imagebox.n2less", array(
            "sliderid" => $owner->getElementID()
        ));
    }
}
