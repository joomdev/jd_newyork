<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');


class N2GeneratorHikaShopProducts extends N2GeneratorAbstract {

    protected $layout = 'product';


    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementHikaShopCategories($source, 'hikashopcategories', n2_('Category'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementHikaShopBrands($source, 'hikashopbrands', n2_('Brand'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementHikaShopTags($source, 'hikashoptags', n2_('Tag'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementHikaShopWarehouses($source, 'hikashopwarehouses', n2_('Warehouse'), 0, array(
            'isMultiple' => true
        ));

        $options = new N2ElementGroup($filter, 'options', n2_('Options'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementMenuItems($filter, 'hikashopitemid', n2_('Menu item (item ID)'), 0);
        new N2ElementOnOff($options, 'hikashopimages', n2_('Include all images'), 1);


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'hikashopproductsorder', n2_('Order'), 'p.product_created|*|desc');
        new N2ElementList($order, 'hikashopproductsorder-1', n2_('Field'), '', array(
            'options' => array(
                ''                        => n2_('None'),
                'p.product_id'            => 'ID',
                'p.product_name'          => n2_('Product name'),
                'p.product_hit'           => n2_('Hits'),
                'p.product_sales'         => n2_('Sales'),
                'p.product_average_score' => n2_('Average score'),
                'p.product_total_vote'    => n2_('Total vote'),
                'p.product_created'       => n2_('Creation time'),
                'p.product_modified'      => n2_('Modification time')
            )
        ));

        new N2ElementRadio($order, 'hikashopproductsorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

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

    protected function _getData($count, $startIndex) {
        require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_hikashop' . DS . 'helpers' . DS . 'helper.php');

        $categories = array_map('intval', explode('||', $this->data->get('hikashopcategories', '')));
        $brands     = array_map('intval', explode('||', $this->data->get('hikashopbrands', '0')));
        $tags       = array_map('intval', explode('||', $this->data->get('hikashoptags', '0')));
        $warehouses = array_map('intval', explode('||', $this->data->get('hikashopwarehouses', '0')));

        $model = new N2Model('hikashop_products');

        $where = array(
            "p.product_published = 1 ",
            "p.product_type <> 'variant'"
        );

        if (!in_array(0, $categories) && count($categories) > 0) {
            $where[] = "p.product_id IN (SELECT product_id FROM #__hikashop_product_category WHERE category_id IN (" . implode(',', $categories) . "))";
        }

        if (!in_array(0, $brands) && count($brands) > 0) {
            $where[] = "p.product_manufacturer_id IN (" . implode(',', $brands) . ")";
        }

        if (!in_array(0, $tags)) {
            $where[] = 'p.product_id IN (SELECT content_item_id FROM #__contentitem_tag_map WHERE type_alias = \'com_hikashop.product\' AND tag_id IN (' . implode(',', $tags) . ')) ';
        }

        if (!in_array(0, $warehouses) && count($warehouses) > 0) {
            $where[] = "p.product_warehouse_id IN (" . implode(',', $warehouses) . ")";
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

        $image_result_array = array();
		$hikashopimages = $this->data->get('hikashopimages', 0);
        if (!empty($hikashopimages)) {
            $id_array = array();
            for ($i = 0; $i < count($result); $i++) {
                $id_array[] = $result[$i]['product_id'];
            }
            $query        = "SELECT file_ref_id, file_path FROM #__hikashop_file WHERE file_ref_id IN(" . implode(",", $id_array) . ") AND file_type = 'product' ORDER BY file_ordering";
            $image_result = $model->db->queryAll($query);
            foreach ($image_result AS $ir) {
                if (!empty($ir['file_path'])) {
                    $image_result_array[$ir['file_ref_id']][] = $ir['file_path'];
                }
            }
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

            if (!empty($image_result_array[$result[$i]['product_id']])) {
                $j = 0;
                foreach ($image_result_array[$result[$i]['product_id']] AS $image) {
                    $j++;
                    $r['image_' . $j] = N2JoomlaImageFallBack::fallback($url, array($folder . $image));
                }
            }

            $data[] = $r;
        }

        return $data;
    }
}
