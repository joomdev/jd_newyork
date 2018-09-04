<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');


class N2GeneratorRedShopProducts extends N2GeneratorAbstract {

    protected $layout = 'product';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementRedShopCategories($source, 'sourcecategories', n2_('Category'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementRedShopManufacturers($source, 'sourcemanufacturers', n2_('Manufacturer'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementRedShopSuppliers($source, 'sourcesuppliers', n2_('Supplier'), 0, array(
            'isMultiple' => true
        ));


        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementFilter($limit, 'sourcefeatured', n2_('Featured'), 0);
        new N2ElementFilter($limit, 'sourceonsale', n2_('On sale'), 0);
        new N2ElementFilter($limit, 'sourceexpired', n2_('Expired'), 0);
        new N2ElementFilter($limit, 'sourceforsell', 'For sell', 0);
        new N2ElementText($limit, 'product_parent_id', 'Parent product ID', '*');


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'redshopproductsorder', n2_('Order'), 'pr.publish_date|*|desc');
        new N2ElementList($order, 'redshopproductsorder-1', n2_('Field'), '', array(
            'options' => array(
                ''                  => n2_('None'),
                'pr.product_name'   => n2_('Product name'),
                'pr_cat.ordering'   => n2_('Ordering'),
                'pr.publish_date'   => n2_('Creation time'),
                'pr.update_date'    => n2_('Modification time'),
                'pr.visited'        => n2_('Hits'),
                'pr.product_price'  => n2_('Price'),
                'pr.discount_price' => n2_('Discount price')
            )
        ));

        new N2ElementRadio($order, 'redshopproductsorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    protected function _getData($count, $startIndex) {

        $where = array(' pr.published = 1 ');

        $categories = array_map('intval', explode('||', $this->data->get('sourcecategories', '')));
        if (!in_array(0, $categories) && count($categories) > 0) {
            $where[] = 'pr_cat.category_id IN (' . implode(',', $categories) . ') ';
        }

        $manufacturers = array_map('intval', explode('||', $this->data->get('sourcemanufacturers', '')));
        if (!in_array(0, $manufacturers) && count($manufacturers) > 0) {
            $where[] = 'pr.manufacturer_id IN (' . implode(',', $manufacturers) . ') ';
        }

        $suppliers = array_map('intval', explode('||', $this->data->get('sourcesuppliers', '')));
        if (!in_array(0, $suppliers) && count($suppliers) > 0) {
            $where[] = 'pr.supplier_id IN (' . implode(',', $suppliers) . ') ';
        }

        switch ($this->data->get('sourcefeatured', 0)) {
            case 1:
                $where[] = ' pr.product_special = 1 ';
                break;
            case -1:
                $where[] = ' pr.product_special = 0 ';
                break;
        }

        switch ($this->data->get('sourceonsale', 0)) {
            case 1:
                $where[] = ' pr.product_on_sale = 1 ';
                break;
            case -1:
                $where[] = ' pr.product_on_sale = 0 ';
                break;
        }

        switch ($this->data->get('sourceexpired', 0)) {
            case 1:
                $where[] = ' pr.expired = 1 ';
                break;
            case -1:
                $where[] = ' pr.expired = 0 ';
                break;
        }

        switch ($this->data->get('sourceforsell', 0)) {
            case 1:
                $where[] = ' pr.not_for_sale = 1 ';
                break;
            case -1:
                $where[] = ' pr.not_for_sale = 0 ';
                break;
        }

        $parentID = $this->data->get('product_parent_id', '*');
        if (is_numeric($parentID)) {
            $where[] = ' pr.product_parent_id = ' . $parentID . ' ';
        }

        $o = '';

        $order = N2Parse::parse($this->data->get('redshopproductsorder', 'pr.product_name|*|asc'));
        if ($order[0]) {
            $o .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $model = new N2Model('redshop_product');

        $query = "SELECT
                        pr.product_id, 
                        pr.published, 
                        pr_cat.ordering, 
                        pr.product_name as name, 
                        pr.product_s_desc as short_description, 
                        pr.product_desc as description,
                        man.manufacturer_name as man_name,
                        pr.product_full_image as image, 
                        pr.product_thumb_image as image_thumbnail, 
                        pr.product_price,
                        pr.discount_price,
                        pr.visited,
                        pr.weight,
                        pr.product_length,
                        pr.product_height,
                        pr.product_width,
                        pr.product_diameter,
                        pr.product_preview_image,
                        pr.product_preview_back_image,
						cat.id,
                        cat.id as category_id,
                        cat.name as category_name, 
                        cat.short_description as category_short_description,
                        cat.description as category_description
                    FROM `#__redshop_product` AS pr
                    LEFT JOIN `#__redshop_product_category_xref` AS pr_cat USING (product_id)
                    LEFT JOIN `#__redshop_category` AS cat ON cat.id = pr_cat.category_id
                    LEFT JOIN `#__redshop_manufacturer` AS man USING(manufacturer_id)
                    WHERE " . implode(' AND ', $where) . " GROUP BY pr.product_id " . $o . " LIMIT " . $startIndex . ", " . $count;

        $result = $model->db->queryAll($query);

        $product = new producthelper;
        //Redconfiguration needed for REDSHOP_FRONT_IMAGES_ABSPATH
        new Redconfiguration;
        $data = array();
        $root = N2Uri::getBaseUri();
        for ($i = 0; $i < count($result); $i++) {

            $r = array(
                'title'             => $result[$i]['name'],
                'url'               => 'index.php?option=com_redshop&view=product&pid=' . $result[$i]['product_id'] . '&cid=' . $result[$i]['category_id'],
                'description'       => $result[$i]['description'],
                'short_description' => $result[$i]['short_description'],
            );

            $r['image'] = N2JoomlaImageFallBack::fallback(REDSHOP_FRONT_IMAGES_ABSPATH . "product/", array(
                @$result[$i]['image'],
                @$result[$i]['product_preview_image'],
                @$result[$i]['image_thumbnail']
            ));

            if (empty($r['image'])) {
                $r['image'] = N2JoomlaImageFallBack::fallback($root . "/", array(), array(
                    $r['description'],
                    $r['short_description']
                ));
            }

            if (!empty($result[$i]['image_thumbnail'])) {
                $r['thumbnail'] = N2ImageHelper::dynamic(REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $result[$i]['image_thumbnail']);
            } else if (!empty($result[$i]['image'])) {
                $r['thumbnail'] = N2ImageHelper::dynamic(REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $result[$i]['image']);
            }

            $r['price'] = $product->getProductFormattedPrice($result[$i]['product_price']);

            if (!empty($result[$i]['product_preview_image'])) {
                $r['product_preview_image'] = N2ImageHelper::dynamic(REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $result[$i]['product_preview_image']);
            }
            if (!empty($result[$i]['product_preview_back_image'])) {
                $r['product_preview_back_image'] = N2ImageHelper::dynamic(REDSHOP_FRONT_IMAGES_ABSPATH . "product/" . $result[$i]['product_preview_back_image']);
            }

            $r += array(
                'unformatted_price'          => $result[$i]['product_price'],
                'discount_price'             => $result[$i]['discount_price'],
                'category_name'              => $result[$i]['category_name'],
                'category_url'               => 'index.php?option=com_redshop&view=category&cid=' . $result[$i]['category_id'] . '&layout=detail',
                'category_description'       => $result[$i]['category_description'],
                'category_short_description' => $result[$i]['category_short_description'],
                'manufacturer_name'          => $result[$i]['man_name'],
                'hits'                       => $result[$i]['visited'],
                'weight'                     => $result[$i]['weight'],
                'product_length'             => $result[$i]['product_length'],
                'product_height'             => $result[$i]['product_height'],
                'product_width'              => $result[$i]['product_width'],
                'product_diameter'           => $result[$i]['product_diameter'],
                'id'                         => $result[$i]['product_id']
            );

            $data[] = $r;
        }

        return $data;
    }

}
