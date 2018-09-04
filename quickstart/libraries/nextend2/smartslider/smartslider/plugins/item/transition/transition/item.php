<?php
N2Loader::import('libraries.renderable.layers.itemFactory', 'smartslider');

class N2SSItemTransition extends N2SSItemAbstract {

    protected $type = 'transition';

    public function render() {
        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $this->loadResources($owner);
        $owner->addScript('new N2Classes.FrontendItemTransition(this, "' . $this->id . '", "' . $this->data->get('animation', 'Fade') . '");');

        $html = N2Html::openTag("div", array(
            "class" => "n2-ss-item-transition-inner n2-ss-img-wrapper n2-ow"
        ));
        $html .= N2Html::tag('img', $owner->optimizeImage($this->data->get('image', '')) + array(
                'alt'   => htmlspecialchars($owner->fill($this->data->get('alt', ''))),
                'class' => 'n2-ss-item-transition-image1 n2-ow'
            ), false);
        $html .= N2Html::tag('img', $owner->optimizeImage($this->data->get('image2', '')) + array(
                'alt'   => htmlspecialchars($owner->fill($this->data->get('alt2', ''))),
                'class' => 'n2-ss-item-transition-image2 n2-ow'
            ), false);
        $html .= N2Html::closeTag('div');

        $linkAttributes = array('class' => 'n2-ow');
        if ($this->isEditor) {
            $linkAttributes['onclick'] = 'return false;';
        }

        return N2Html::tag("div", array(
            "id"    => $this->id,
            "class" => "n2-ss-item-transition"
        ), $this->getLink($html, $linkAttributes));
    }

    /**
     * @param $owner N2SmartSliderComponentOwnerAbstract
     */
    public function loadResources($owner) {

        $owner->addLess(dirname(__FILE__) . "/transition.n2less", array(
            "sliderid" => $owner->getElementID()
        ));
    }

}
