<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');


class N2GeneratorMijoShopProducts extends N2GeneratorAbstract {

    protected $layout = 'product';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementMijoShopCategories($source, 'mijoshopsourcecategories', n2_('Category'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementMijoShopManufacturers($source, 'mijoshopsourcemanufacturers', n2_('Manufacturer'), 0, array(
            'isMultiple' => true
        ));


        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementFilter($limit, 'mijoshopsourcespecial', 'Special', 0);
        new N2ElementFilter($limit, 'mijoshopsourceinstock', n2_('In stock'), 0);
        new N2ElementMijoShopLanguages($limit, 'mijoshopsourcelanguage', n2_('Language'), '');


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'mijoshoporder', n2_('Order'), 'p.date_added|*|desc');
        new N2ElementList($order, 'mijoshoporder-1', n2_('Field'), '', array(
            'options' => array(
                ''                => n2_('None'),
                'pc.name'         => n2_('Product name'),
                'p.sort_order'    => n2_('Ordering'),
                'p.viewed'        => n2_('Viewed'),
                'p.price'         => n2_('Price'),
                'p.date_added'    => n2_('Creation time'),
                'p.date_modified' => n2_('Modification time')
            )
        ));

        new N2ElementRadio($order, 'mijoshoporder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    protected function _getData($count, $startIndex) {

        require_once(JPATH_ROOT . '/components/com_mijoshop/mijoshop/mijoshop.php');
        require_once(JPATH_ROOT . '/components/com_mijoshop/opencart/system/library/url.php');

        $config   = MijoShop::get('opencart')
                            ->get('config');
        $currency = MijoShop::get('opencart')
                            ->get('currency');

        if (MijoShop::get('base')
                    ->isAdmin('joomla')
        ) {
            require_once(JPATH_ROOT . '/components/com_mijoshop/opencart/system/library/tax.php');
            MijoShopOpencart::$tax = new Tax(MijoShopOpencart::$registry);
            MijoShopOpencart::$registry->set('tax', MijoShopOpencart::$tax);
        }

        $tax    = MijoShop::get('opencart')
                          ->get('tax');
        $router = MijoShop::get('router');

        $language_id = intval($this->data->get('mijoshopsourcelanguage'));
        if (!$language_id) $language_id = intval($config->get('config_language_id'));

        $tmpLng = $config->get('config_language_id');
        $config->set('config_language_id', $language_id);

        MijoShopOpencart::$loader->model('catalog/product');
        $p = new ModelCatalogProduct(MijoShopOpencart::$registry);

        $model = new N2Model('mijoshop_product');
        $query = 'SELECT ';
        $query .= 'p.product_id ';

        $where = array(' p.status = 1 ');
        switch ($this->data->get('mijoshopsourcespecial', 0)) {
            case 0:
                $query .= ', ps.price AS special_price ';
                break;
            case 1:
                $query .= ', ps.price AS special_price ';

                $where[] = ' ps.price IS NOT NULL';
                $jNow    = JFactory::getDate();
                $now     = $jNow->toSql();
                $where[] = ' (ps.date_start = "0000-00-00" OR ps.date_start < \'' . $now . '\')';
                $where[] = ' (ps.date_end = "0000-00-00" OR ps.date_end > \'' . $now . '\')';
                break;
            case -1:
                $jNow    = JFactory::getDate();
                $now     = $jNow->toSql();
                $where[] = ' (ps.price IS NULL OR (ps.date_start > \'' . $now . '\' OR ps.date_end < \'' . $now . '\' AND ps.date_end <> "0000-00-00"))';
                break;
        }

        $query .= 'FROM #__mijoshop_product AS p ';

        $query .= 'LEFT JOIN #__mijoshop_product_description AS pc USING(product_id) ';
        $query .= 'LEFT JOIN #__mijoshop_product_to_category AS ptc USING(product_id) ';
        $query .= 'LEFT JOIN #__mijoshop_product_special AS ps USING(product_id) ';

        $categories = array_map('intval', explode('||', $this->data->get('mijoshopsourcecategories', '0')));

        if (!in_array(0, $categories) && count($categories) > 0) {
            $where[] = 'ptc.category_id IN (' . implode(',', $categories) . ') ';
        }

        $manufacturers = array_map('intval', explode('||', $this->data->get('mijoshopmanufacturers', '0')));

        if (!in_array(0, $manufacturers) && count($manufacturers) > 0) {
            $where[] = 'p.manufacturer_id IN (' . implode(',', $manufacturers) . ') ';
        }

        switch ($this->data->get('mijoshopsourceinstock', 0)) {
            case 1:
                $where[] = ' p.quantity > 0 ';
                break;
            case -1:
                $where[] = ' p.quantity = 0 ';
                break;
        }

        $where[] = ' pc.language_id  = ' . $language_id;

        if (count($where) > 0) {
            $query .= 'WHERE ' . implode(' AND ', $where) . ' ';
        }

        $query .= 'GROUP BY p.product_id ';

        $order = N2Parse::parse($this->data->get('mijoshoporder', 'pc.name|*|asc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }
        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $data = array();
        $root = N2Uri::getBaseUri();
        for ($i = 0; $i < count($result); $i++) {

            $pi = $p->getProduct($result[$i]['product_id']);

            $r = array(
                'title'       => $pi['name'],
                'url'         => $router->route('index.php?option=com_mijoshop&route=product/product&product_id=' . $pi['product_id']),
                'description' => html_entity_decode($pi['description'])
            );
            if (!empty($pi['image'])) {
                $r['image'] = N2ImageHelper::dynamic(N2Filesystem::pathToAbsoluteURL(DIR_IMAGE) . $pi['image']);
            } else {
                $r['image'] = N2JoomlaImageFallBack::fallback($root . "/", array(), array($r['description']));
            }

            $r += array(
                'thumbnail' => $r['image'],
                'price'     => $currency->format($tax->calculate($pi['price'], $pi['tax_class_id'], $config->get('config_tax')))
            );
            if (!empty($result[$i]['special_price'])) {
                $r['special_price'] = $currency->format($tax->calculate($result[$i]['special_price'], $pi['tax_class_id'], $config->get('config_tax')));
            }

            if ($config->get('config_tax')) {
                $r['price_without_tax'] = $currency->format((float)$result[$i]['special_price'] ? $result[$i]['special_price'] : $pi['price']);
            }

            $r += array(
                'model'    => $pi['model'],
                'sku'      => $pi['sku'],
                'upc'      => $pi['upc'],
                'ean'      => $pi['ean'],
                'jan'      => $pi['jan'],
                'isbn'     => $pi['isbn'],
                'mpn'      => $pi['mpn'],
                'location' => $pi['location'],
                'weight'   => $pi['weight'],
                'length'   => $pi['length'],
                'width'    => $pi['width'],
                'height'   => $pi['height'],
                'tag'      => $pi['tag']
            );
            $data[] = $r;
        }

        $config->set('config_language_id', $tmpLng);

        return $data;
    }

}
