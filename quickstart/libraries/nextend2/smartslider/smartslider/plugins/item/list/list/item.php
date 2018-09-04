<?php
N2Loader::import('libraries.renderable.layers.itemFactory', 'smartslider');

class N2SSItemList extends N2SSItemAbstract {

    protected $type = 'list';

    public function render() {
        return $this->getHtml();
    }

    public function _renderAdmin() {
        return $this->getHtml();
    }


    private function getHTML() {
        $owner = $this->layer->getOwner();

        $font      = $owner->addFont($this->data->get('font'), 'list');
        $listStyle = $owner->addStyle($this->data->get('liststyle'), 'heading');
        $itemStyle = $owner->addStyle($this->data->get('itemstyle'), 'heading');


        $html = '';
        $lis  = explode("\n", $owner->fill($this->data->get('content', '')));
        foreach ($lis AS $li) {
            $html .= '<li class="' . $itemStyle . ' n2-ow">' . $li . '</li>';
        }

        return N2Html::tag('ol', array(
            'class' => $font . '' . $listStyle . ' n2-ow',
            'style' => "list-style-type:" . $this->data->get('type')
        ), $html);
    }
}
