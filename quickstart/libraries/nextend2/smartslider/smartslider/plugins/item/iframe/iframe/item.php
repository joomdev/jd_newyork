<?php
N2Loader::import('libraries.renderable.layers.itemFactory', 'smartslider');

class N2SSItemIframe extends N2SSItemAbstract {

    protected $type = 'iframe';

    public function render() {
        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $size = (array)N2Parse::parse($this->data->get('size', ''));
        if (!isset($size[0])) $size[0] = '100%';
        if (!isset($size[1])) $size[1] = '100%';

        $attributes = array(
            "encode"      => false,
            "frameborder" => 0,
            "class"       => "n2-ow",
            "width"       => $size[0],
            "height"      => $size[1],
            "scrolling"   => $this->data->get("scroll"),
            "sandbox"     => 'allow-modals allow-forms allow-popups allow-scripts allow-same-origin'
        );

        $attributes[$owner->isLazyLoadingEnabled() ? 'data-lazysrc' : 'src'] = $owner->fill($this->data->get("url"));

        return N2Html::tag('div', array('class' => 'n2-ss-item-iframe-wrapper'), N2Html::tag("iframe", $attributes, ""));
    }

    public function needSize() {
        return true;
    }
}
