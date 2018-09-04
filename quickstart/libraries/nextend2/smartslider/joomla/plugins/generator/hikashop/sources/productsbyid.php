<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');


class N2GeneratorHikaShopProductsbyid extends N2GeneratorAbstract {

    protected $layout = 'product';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        new N2ElementTextarea($filter, 'ids', 'IDs - ' . n2_('One per line'), '', array(
            'fieldStyle' => 'width:300px;height: 200px;'
        ));

        new N2ElementMenuItems($filter, 'hikashopitemid', n2_('Menu item (item ID)'), 0);
    }

    function getPrice($pid, $tax_id = 0) {
        $arr                    = array();
        $arr[0]                 = new stdClass();
        $arr[0]->product_id     = $pid;
        $arr[0]->product_tax_id = $tax_id;
        $currencyClass          = hikashop_get('class.currency');
        $zone                   = hikashop_getZone();
        $cur                    = hikashop_getCurrency();
        $currencyClass->getListingPrices($arr, $zone, $cur);
        $i         = 0;
        $currPrice = 0;
        if (isset($arr[0]->prices)) {
            foreach ($arr[0]->prices as $k => $price) {
                if (!$i) {
                    $currPrice = $price->price_value_with_tax;
                }
                if ($price->price_value_with_tax < $currPrice) $currPrice = $price->price_value_with_tax;
                $i++;
            }

            return $currencyClass->format($currPrice, $cur);
        } else {
            return '';
        }
    }

    function url($id, $alias, $itemID) {
        $url = 'index.php?option=com_hikashop&ctrl=product&task=show&cid=' . $id;
        if (!empty($alias)) {
            $url .= '&name=' . $alias;
        }
        if (!empty($itemID) && $itemID != 0) {
            $url .= '&Itemid=' . $itemID;
        }

        return $url;
    }

    function getProductIDs() {
        return array_map('intval', explode("\n", str_replace(array(
            "\r\n",
            "\n\r",
            "\r"
        ), "\n", $this->data->get('ids'))));
    }

    protected function _getData($count, $startIndex) {
        require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_hikashop' . DS . 'helpers' . DS . 'helper.php');

        $idarray = self::getProductIDs();

        $model = new N2Model('hikashop_products');

        $where = array(
            "p.product_published = 1 "
        );

        if (count($idarray) > 0) {
            $where[] = "p.product_id IN (" . implode(',', $idarray) . ")";
        }

        $query = "SELECT * FROM #__hikashop_product AS p LEFT JOIN #__hikashop_file AS f ON p.product_id = f.file_ref_id AND f.file_type='product' WHERE " . implode(' AND ', $where);

        $query .= " GROUP BY p.product_id ";

        $order = N2Parse::parse($this->data->get('hikashopproductsorder', 'p.product_created|*|desc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1];
        }

        $query .= ' LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        if (function_exists('hikashop_config')) {
            $config = hikashop_config();
            $folder = $config->get('uploadfolder');
            if (empty($folder)) {
                $folder = 'media/com_hikashop/upload/';
            } else if (substr($folder, -1) != '/') {
                $folder .= '/';
            }
        } else {
            $folder = 'media/com_hikashop/upload/';
        }

        $data = array();
        $url  = JURI::root(false);
        for ($i = 0; $i < count($result); $i++) {
            $r = array(
                'title'       => $result[$i]['product_name'],
                'url'         => $this->url($result[$i]['product_id'], $result[$i]['product_alias'], $this->data->get('hikashopitemid', '0')),
                'description' => $result[$i]['product_description']
            );

            $r['image'] = N2JoomlaImageFallBack::fallback($url, array(
                !empty($result[$i]['file_path']) ? $folder . $result[$i]['file_path'] : '',
            ), array(
                @$r['description']
            ));

            if (!empty($result[$i]['file_path'])) {
                $r['thumbnail'] = str_replace($folder, $folder . 'thumbnails/100x100/', $r['image']);
            } else {
                $r['thumbnail'] = $r['image'];
            }

            $r += array(
                'price'                    => $this->getPrice($result[$i]['product_id'], $result[$i]['product_tax_id']),
                'price_without_tax'        => $this->getPrice($result[$i]['product_id']),
                'product_code'             => $result[$i]['product_code'],
                'hits'                     => $result[$i]['product_hit'],
                'brand_url'                => $result[$i]['product_url'],
                'product_weight'           => $result[$i]['product_weight'],
                'product_weight_unit'      => $result[$i]['product_weight_unit'],
                'product_keywords'         => $result[$i]['product_keywords'],
                'product_meta_description' => $result[$i]['product_meta_description'],
                'product_width'            => $result[$i]['product_width'],
                'product_length'           => $result[$i]['product_length'],
                'product_height'           => $result[$i]['product_height'],
                'product_dimension_unit'   => $result[$i]['product_dimension_unit'],
                'product_sales'            => $result[$i]['product_sales'],
                'product_average_score'    => $result[$i]['product_average_score'],
                'product_total_vote'       => $result[$i]['product_total_vote'],
                'product_page_title'       => $result[$i]['product_page_title'],
                'product_alias'            => $result[$i]['product_alias'],
                'product_price_percentage' => $result[$i]['product_price_percentage'],
                'product_msrp'             => $result[$i]['product_msrp'],
                'product_canonical'        => $result[$i]['product_canonical'],
                'product_id'               => $result[$i]['product_id']
            );
            $data[] = $r;
        }

        return $data;
    }
}
