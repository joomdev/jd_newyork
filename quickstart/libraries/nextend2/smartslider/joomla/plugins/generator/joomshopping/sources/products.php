<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');


class N2GeneratorJoomShoppingProducts extends N2GeneratorAbstract {

    protected $layout = 'product';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementJoomShoppingCategories($source, 'sourcecategories', n2_('Category'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementJoomShoppingManufacturers($source, 'sourcemanufacturers', n2_('Manufacturer'), 0, array(
            'isMultiple' => true
        ));


        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementFilter($limit, 'sourceinstock', n2_('In stock'), 0);
        new N2ElementJoomShoppingLabels($limit, 'sourcelabel', n2_('Label'), -1);

        new N2ElementMenuItems($limit, 'itemid', n2_('Menu item (item ID)'), 0);
        new N2ElementText($limit, 'language', n2_('Language'), '', array(
            'tip' => 'en-GB,de-DE,hu-HU,...'
        ));


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'productsorder', n2_('Order'), 'pr.product_date_added|*|desc');
        new N2ElementList($order, 'productsorder-1', n2_('Field'), '', array(
            'options' => array(
                ''                        => n2_('None'),
                'pr.name'                 => n2_('Product name'),
                'category_name'           => n2_('Category'),
                'pr_cat.product_ordering' => n2_('Ordering'),
                'pr.hits'                 => n2_('Hits'),
                'pr.product_date_added'   => n2_('Creation time'),
                'pr.date_modify'          => n2_('Modification time')
            )
        ));

        new N2ElementRadio($order, 'productsorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    protected function _getData($count, $startIndex) {

        require_once(JPATH_SITE . "/components/com_jshopping/lib/factory.php");

        $jShopConfig = JSFactory::getConfig();
        $lang        = JSFactory::getLang();
		$language    = $this->data->get('language', '');
        $customLang  = !empty($language);
        if ($customLang) {
            $lang = $this->data->get('language', '');
        }
        $session = JFactory::getSession();

        $where = array(' pr.product_publish = 1 ');

        $category = array_map('intval', explode('||', $this->data->get('sourcecategories', '')));
        if (!in_array(0, $category) && count($category) > 0) {
            $where[] = 'pr_cat.category_id IN (' . implode(',', $category) . ') ';
        }

        $manufacturers = array_map('intval', explode('||', $this->data->get('sourcemanufacturers', '')));
        if (!in_array(0, $manufacturers) && count($manufacturers) > 0) {
            $where[] = 'pr.product_manufacturer_id IN (' . implode(',', $manufacturers) . ') ';
        }

        switch ($this->data->get('sourceinstock', 0)) {
            case 1:
                $where[] = ' (pr.product_quantity > 0 OR pr.unlimited = 1) ';
                break;
            case -1:
                $where[] = ' (pr.product_quantity = 0 AND pr.unlimited = 0) ';
                break;
        }

        $label_id = intval($this->data->get('sourcelabel', -1));

        if ($label_id != -1) {
            $where[] = ' pr.label_id = "' . $label_id . '" ';
        }

        $o     = '';
        $order = N2Parse::parse($this->data->get('productsorder', 'pr.name|*|asc'));
        if ($order[0]) {
            if ($order[0] == 'pr.name') $order[0] = 'pr.`' . $lang->get('name') . '`';
            $o .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query = "SELECT 
                        pr.product_id, 
                        pr.product_publish, 
                        pr_cat.product_ordering, ";

        if ($customLang) {
            $query .= " pr.`name_" . $lang . "` as name,
                        pr.`short_description_" . $lang . "` as short_description,
                        pr.`description_" . $lang . "` as description,
                        man.`name_" . $lang . "` as man_name,";
        } else {
            $query .= " pr.`" . $lang->get('name') . "` as name,
                        pr.`" . $lang->get('short_description') . "` as short_description,
                        pr.`" . $lang->get('description') . "` as description,
                        man.`" . $lang->get('name') . "` as man_name,";
        }

        $query .= "     pr.product_ean as ean,
                        pr.product_quantity as qty,
                        pri.image_name as image,
                        pr.product_price,
                        pr.currency_id,
                        pr.hits,
                        pr.unlimited,
                        pr.product_date_added,
                        pr.label_id,
                        pr.vendor_id,
                        V.f_name as v_f_name,
                        V.l_name as v_l_name,
                        cat.category_image,
                        cat.category_id,";

        if ($customLang) {
            $query .= " cat.`name_" . $lang . "` as category_name,
                        cat.`alias_" . $lang . "` as category_alias,
                        cat.`short_description_" . $lang . "` as category_short_description,
                        cat.`description_" . $lang . "` as category_description";
        } else {
            $query .= " cat.`" . $lang->get('name') . "` as category_name,
                        cat.`" . $lang->get('alias') . "` as category_alias,
                        cat.`" . $lang->get('short_description') . "` as category_short_description,
                        cat.`" . $lang->get('description') . "` as category_description";
        }

        $query .= " FROM `#__jshopping_products` AS pr
                    LEFT JOIN `#__jshopping_products_to_categories` AS pr_cat USING (product_id)
                    LEFT JOIN `#__jshopping_categories` AS cat USING (category_id)
                    LEFT JOIN `#__jshopping_manufacturers` AS man ON pr.product_manufacturer_id=man.manufacturer_id
                    LEFT JOIN `#__jshopping_vendors` as V on pr.vendor_id=V.id
                    LEFT JOIN `#__jshopping_products_images` as pri on pr.product_id=pri.product_id
                    WHERE pr.parent_id=0 " . (count($where) ? ' AND ' . implode(' AND ', $where) : '') . " GROUP BY pr.product_id " . $o . " LIMIT " . $startIndex . ", " . $count;

        $model = new N2Model('jshopping_products');

        $result = $model->db->queryAll($query);

        $data = array();

        $root = N2Uri::getBaseUri();

        $itemID = $this->data->get('itemid', '0');

        for ($i = 0; $i < count($result); $i++) {
            $product = JTable::getInstance('product', 'jshop');
            $product->load($result[$i]['product_id']);

            $attr       = JRequest::getVar("attr");
            $back_value = $session->get('product_back_value');
            if (!isset($back_value['pid'])) $back_value = array(
                'pid'  => null,
                'attr' => null,
                'qty'  => null
            );
            if ($back_value['pid'] != $result[$i]['product_id']) $back_value = array(
                'pid'  => null,
                'attr' => null,
                'qty'  => null
            );
            if (!is_array($back_value['attr'])) $back_value['attr'] = array();
            if (count($back_value['attr']) == 0 && is_array($attr)) $back_value['attr'] = $attr;
            $attributesDatas = $product->getAttributesDatas($back_value['attr']);
            $product->setAttributeActive($attributesDatas['attributeActive']);

            getDisplayPriceForProduct($product->product_price);
            $product->getExtendsData();

            $r = array(
                'title'             => $result[$i]['name'],
                'url'               => SEFLink('index.php?option=com_jshopping&controller=product&task=view&product_id=' . $result[$i]['product_id'] . '&category_id=' . $result[$i]['category_id']),
                'joomla_url'        => 'index.php?option=com_jshopping&controller=product&task=view&product_id=' . $result[$i]['product_id'] . '&category_id=' . $result[$i]['category_id'] . '&Itemid=' . $itemID,
                'description'       => $result[$i]['description'],
                'short_description' => $result[$i]['short_description']
            );

            $op = $product->getOldPrice();

            if ($result[$i]['image'] != null) {
                $r += array(
                    'image'      => N2ImageHelper::dynamic($jShopConfig->image_product_live_path . '/' . $result[$i]['image']),
                    'thumbnail'  => N2ImageHelper::dynamic($jShopConfig->image_product_live_path . '/thumb_' . $result[$i]['image']),
                    'image_full' => N2ImageHelper::dynamic($jShopConfig->image_product_live_path . '/full_' . $result[$i]['image'])
                );
            } else {
                $image      = N2JoomlaImageFallBack::findImage($r['description']);
                $r['image'] = $r['thumbnail'] = N2JoomlaImageFallBack::fallback($root . "/", array($image));
            }

            $r += array(
                'price'                      => formatprice($product->getPriceCalculate()),
                'product_old_price'          => $op > 0 ? formatprice($op) : '',
                'category_name'              => $result[$i]['category_name'],
                'category_short_description' => $result[$i]['category_short_description'],
                'category_description'       => $result[$i]['category_description'],
                'category_url'               => SEFLink('index.php?option=com_jshopping&controller=category&task=view&category_id=' . $result[$i]['category_id']),
                'add_to_cart_url'            => SEFLink('index.php?option=com_jshopping&controller=cart&task=add&quantity=1&to=cart&product_id=' . $result[$i]['product_id'] . '&category_id=' . $result[$i]['category_id']),
                'manufacturer_name'          => $result[$i]['man_name'],
                'product_id'                 => $result[$i]['product_id']
            );

            $data[] = $r;
        }

        return $data;
    }
}