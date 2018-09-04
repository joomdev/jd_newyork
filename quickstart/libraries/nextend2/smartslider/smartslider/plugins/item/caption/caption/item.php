<?php
N2Loader::import('libraries.renderable.layers.itemFactory', 'smartslider');

class N2SSItemCaption extends N2SSItemAbstract {

    protected $type = 'caption';

    public function render() {
        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $this->loadResources($owner);

        list($mode, $direction, $scale) = N2Parse::parse($this->data->get('animation', 'Simple|*|left|*|0'));
        $owner->addScript('new N2Classes.FrontendItemCaption(this, "' . $this->id . '", "' . $mode . '", "' . $direction . '", ' . intval($scale) . ');');

        $html = N2Html::tag('div', array('class' => 'n2-ss-img-wrapper n2-ow'), N2Html::tag('img', $owner->optimizeImage($this->data->get('image', '')) + array(
                'alt'   => htmlspecialchars($owner->fill($this->data->get('alt', ''))),
                'class' => 'n2-ow'
            ), false));

        list($hex, $rgba) = N2Color::colorToCss($this->data->get('color', '00000080'));
        $html .= N2Html::openTag("div", array(
            "class"              => "n2-ss-item-caption-content n2-ow",
            "style"              => "background:#{$hex}; background: {$rgba};",
            "data-verticalalign" => $this->data->get('verticalalign', 'center')
        ));

        $title = $owner->fill($this->data->get('content', ''));
        if ($title != '') {
            $fontTitle = $owner->addFont($this->data->get('fonttitle'), 'paragraph');
            $html .= N2Html::tag("div", array("class" => 'n2-ow n2-div-h4 ' . $fontTitle), $title);
        }

        $description = $owner->fill($this->data->get('description', ''));
        if ($description != '') {
            $font = $owner->addFont($this->data->get('font'), 'paragraph');
            $html .= N2Html::tag("p", array("class" => 'n2-ow ' . $font), $description);
        }

        $html .= N2Html::closeTag("div");

        $linkAttributes = array(
            'class' => 'n2-ow'
        );
        if ($this->isEditor) {
            $linkAttributes['onclick'] = 'return false;';
        }

        return N2Html::tag("div", array(
            "id"    => $this->id,
            "class" => "n2-ss-item-caption n2-ow"
        ), $this->getLink($html, $linkAttributes));
    }

    /**
     * @param N2SmartSliderComponentOwnerAbstract $owner
     */
    public function loadResources($owner) {

        $owner->addLess(dirname(__FILE__) . "/caption.n2less", array(
            "sliderid" => $owner->getElementID()
        ));
    }
}
