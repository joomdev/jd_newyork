<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorEcwidProducts extends N2GeneratorAbstract {

    protected $layout = 'product';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        new N2ElementEcwidCategories($filter, 'categories', n2_('Category'), '', array(
            'configuration' => $this->group->getConfiguration()
        ));
        new N2ElementNumber($filter, 'tax', n2_('Tax'), 0, array(
            'wide' => 4,
            'unit' => '%'
        ));
    }

    protected function _getData($count, $startIndex) {

        $tax = (intval($this->data->get('tax', 0)) + 100) / 100;

        $store = $this->group->getConfiguration()
            ->getStoreID();

        $apiURL = 'http://app.ecwid.com/api/v1/' . $store . '/products';

        $categoryID = intval($this->data->get('categories', ''));
        if ($categoryID > 0) {
            $apiURL .= '?category=' . $categoryID;
        } else if($categoryID == "-1"){
            $apiURL .= '?category=0';
        }

        $data = array();

        $json = N2TransferData::get($apiURL);
        if (!$json) {
            return null;
        }
        $products = json_decode($json);
        if (is_array($products)) {
            for ($i = 0; $i < count($products); $i++) {
                if (isset($products[$i]->name)) {
                    $data[$i]['title'] = $products[$i]->name;
                }

                if (isset($products[$i]->url)) {
                    $data[$i]['url'] = $products[$i]->url;
                }

                if (isset($products[$i]->description)) {
                    $data[$i]['description'] = $products[$i]->description;
                }

                if (isset($products[$i]->imageUrl)) {
                    $data[$i]['image'] = $products[$i]->imageUrl;
                }

                if (isset($products[$i]->thumbnailUrl)) {
                    $data[$i]['thumbnail'] = $products[$i]->thumbnailUrl;
                }

                if (isset($products[$i]->price)) {
                    $data[$i]['price']       = $products[$i]->price;
                    $data[$i]['price_w_tax'] = money_format('%i', $products[$i]->price * $tax);
                }

                if (isset($products[$i]->sku)) {
                    $data[$i]['sku'] = $products[$i]->sku;
                }

                if (isset($products[$i]->quantity)) {
                    $data[$i]['quantity'] = $products[$i]->quantity;
                }

                if (isset($products[$i]->weight)) {
                    $data[$i]['weight'] = $products[$i]->weight;
                }

                if (isset($products[$i]->smallThumbnailUrl)) {
                    $data[$i]['smallThumbnailUrl'] = $products[$i]->smallThumbnailUrl;
                }

                if (isset($products[$i]->created)) {
                    $data[$i]['created'] = $products[$i]->created;
                }

                if (isset($products[$i]->id)) {
                    $data[$i]['id'] = $products[$i]->id;
                }
            }
        } else {
            return null;
        }

        return array_slice($data, $startIndex, $count);
    }

}
