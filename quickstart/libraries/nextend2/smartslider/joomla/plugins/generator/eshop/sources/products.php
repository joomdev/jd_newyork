<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');


class N2GeneratorEShopProducts extends N2GeneratorAbstract {

    protected $layout = 'product';

    var $leftSymbol = '';
    var $rightSymbol = '';
    var $decimalPlace = '';
    var $currentTime = '';
    var $exchangeValue = '';
    var $decimalPoint = '';
    var $thousandsSeparator = '';
    var $categoryTree = array();


    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementEShopCategories($source, 'eshopsourcecategories', n2_('Categories'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementEShopManufacturers($source, 'eshopsourcemanufacturers', n2_('Manufacturers'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementEShopTags($source, 'eshopsourcetags', n2_('Tags'), 0, array(
            'isMultiple' => true
        ));

        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementFilter($limit, 'eshopsourcefeatured', n2_('Featured'), 0);
        new N2ElementFilter($limit, 'eshopsourcediscount', n2_('On discount'), 0);
        new N2ElementFilter($limit, 'eshopsourceinstock', n2_('In stock'), 0);
        new N2ElementOnOff($limit, 'eshopsourcesubcategory', n2_('Include subcategories'), 0);


        $language = new N2ElementGroup($filter, 'language', n2_('Language'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementEShopCurrency($language, 'eshopsourcecurrencycode', n2_('Currency'), 0);
        new N2ElementEShopProductLanguage($language, 'eshopsourceproductlanguage', n2_('Product'), 0);
        new N2ElementEShopCategoryLanguage($language, 'eshopsourcecategorylanguage', n2_('Category'), 0);
        new N2ElementEShopManufacturerLanguage($language, 'eshopsourcemanufacturerlanguage', n2_('Manufacturer'), 0);

        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'eshoporder', n2_('Order'), 'p.created_date|*|desc');
        new N2ElementList($order, 'eshoporder-1', n2_('Field'), '', array(
            'options' => array(
                ''                => n2_('None'),
                'p.product_price' => n2_('Price'),
                'pd.product_name' => n2_('Product name'),
                'p.ordering'      => n2_('Ordering'),
                'p.hits'          => n2_('Hits'),
                'p.created_date'  => n2_('Creation time'),
                'p.modified_date' => n2_('Modification time'),
                'p.id'            => n2_('Product ID')
            )
        ));

        new N2ElementRadio($order, 'eshoporder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    function setCurrencyDetails($left, $right, $dec, $point, $thou, $now, $exchange) {
        $this->leftSymbol           = $left;
        $this->rightSymbol          = $right;
        $this->decimalPlace         = $dec;
        $this->decimalPoint         = $point;
        $this->thousandsSeparator   = $thou;
        $this->currentTime          = $now;
        $this->exchangeValue        = $exchange;
    }

    function decimals($var) {
        if(!empty($this->decimalPlace)){
          return number_format($var, $this->decimalPlace, $this->decimalPoint, $this->thousandsSeparator );
        } else {
          return round($var);
        }
    }

    function createPrice($product_price, $discount_price = null, $discount_date_start = null, $discount_date_end = null, $symbol = true) {
        if ($symbol) {
            $price = $this->leftSymbol;
        } else {
            $price = '';
        }
        if (!empty($discount_price)) {
            if (($discount_date_start == '0000-00-00 00:00:00' || $discount_date_start <= $this->currentTime) && ($discount_date_end == '0000-00-00 00:00:00' || $discount_date_end > $this->currentTime)) {
                $product_price = $discount_price;
            }
        }
        $product_price = $this->exchangeValue * $product_price;
        $price .= $this->decimals($product_price);
        $price .= $this->rightSymbol;

        return $price;
    }

    function buildCategoryTree($categoryID) {
        $categories = EshopHelper::getCategories($categoryID);
        if (!empty($categories)) {
            foreach ($categories as $cat) {
                $this->categoryTree[] = $cat->id;
                $this->buildCategoryTree($cat->id);
            }
        }
    }

    protected function _getData($count, $startIndex) {
    		if(!class_exists('EshopRoute')){
    			require_once(JPATH_SITE . '/components/com_eshop/helpers/helper.php');
    			require_once(JPATH_SITE . '/components/com_eshop/helpers/route.php');
    		}

        $model = new N2Model('eshop_products');

        $categories    = array_map('intval', explode(' || ', $this->data->get('eshopsourcecategories', '0')));
        $manufacturers = array_map('intval', explode(' || ', $this->data->get('eshopsourcemanufacturers', '0')));
        $tags          = array_map('intval', explode(' || ', $this->data->get('eshopsourcetags', '0')));

        if ($this->data->get('eshopsourcesubcategory', '0') == 1) {
            foreach ($categories as $cat) {
                $this->buildCategoryTree($cat);
            }
            $categories = $this->categoryTree;
        }

        $where = array('p . published = 1');
        if (!in_array(0, $categories) && count($categories) > 0) {
            $where[] = 'pc . category_id IN(' . implode(', ', $categories) . ') ';
        }
        if (!in_array(0, $manufacturers) && count($manufacturers) > 0) {
            $where[] = 'p . manufacturer_id IN(' . implode(', ', $manufacturers) . ') ';
        }
        if (!in_array(0, $tags) && count($tags) > 0) {
            $where[] = 'pt . tag_id IN(' . implode(', ', $tags) . ') ';
        }

        switch ($this->data->get('eshopsourcefeatured', 0)) {
            case 1:
                $where[] = 'p . product_featured = 1 ';
                break;
            case -1:
                $where[] = 'p . product_featured = 0 ';
                break;
        }

        $jNow = JFactory::getDate();
        $now  = $jNow->toSql();
        switch ($this->data->get('eshopsourcediscount', 0)) {
            case 1:
                $where[] = "p.id IN (SELECT product_id FROM #__eshop_productdiscounts WHERE
        date_start = '0000-00-00 00:00:00' OR date_start <= '" . $now . "' AND date_end = '0000-00-00 00:00:00' OR date_end > '" . $now . "') ";
                break;
            case -1:
                $where[] = "p.id NOT IN (SELECT product_id FROM #__eshop_productdiscounts WHERE
        date_start = '0000-00-00 00:00:00' OR date_start <= '" . $now . "' AND date_end = '0000-00-00 00:00:00' OR date_end > '" . $now . "') ";
                break;
        }

        switch ($this->data->get('eshopsourceinstock', 0)) {
            case 1:
                $where[] = "p.product_quantity > 0";
                break;
            case -1:
                $where[] = "product_quantity = 0";
                break;
        }

        $prodLang = $this->data->get('eshopsourceproductlanguage', '');
        if (!empty($prodLang)) {
            $where[] = "pd.language = '" . $prodLang . "'";
        }

        $catLang = $this->data->get('eshopsourcecategorylanguage', '');
        if (!empty($catLang)) {
            $where[] = "cd.language = '" . $catLang . "'";
        }

        $manLang = $this->data->get('eshopsourcemanufacturerlanguage', '');
        if (!empty($manLang)) {
            $where[] = "md.language = '" . $manLang . "'";
        }

        $currencyCode = $this->data->get('eshopsourcecurrencycode', '');
        if (!empty($currencyCode)) {
            $where[] = "cu.currency_code = '" . $currencyCode . "'";
        } else {
            $where[] = "cu.currency_code = (SELECT config_value FROM #__eshop_configs WHERE config_key = 'default_currency_code' LIMIT 1)";
        }

        $query = "SELECT *, cow.config_value AS image_thumb_width, coh.config_value AS image_thumb_height, p.id AS id
                  FROM #__eshop_products AS p
                  LEFT JOIN #__eshop_productcategories AS pc ON p.id = pc.product_id
                  LEFT JOIN #__eshop_productdetails AS pd ON p.id = pd.product_id
                  LEFT JOIN #__eshop_productimages AS pi ON p.id = pi.product_id
                  LEFT JOIN #__eshop_productdiscounts AS pdi ON p.id = pdi.product_id
                  LEFT JOIN #__eshop_producttags as pt ON p.id = pt.product_id
                  LEFT JOIN #__eshop_categories as c ON c.id = pc.category_id
                  LEFT JOIN #__eshop_categorydetails as cd ON cd.category_id = pc.category_id
                  LEFT JOIN #__eshop_manufacturers as m ON p.manufacturer_id = m.id
                  LEFT JOIN #__eshop_manufacturerdetails AS md ON p.manufacturer_id = md.manufacturer_id
                  CROSS JOIN #__eshop_currencies AS cu
                  CROSS JOIN #__eshop_configs AS cow
                  CROSS JOIN #__eshop_configs AS coh
                  WHERE cow.config_key = 'image_thumb_width' AND coh.config_key = 'image_thumb_height' AND " . implode(' AND ', $where) . " ";

        $order = N2Parse::parse($this->data->get('eshoporder', 'p . created_date |*|desc'));
        if ($order[0]) {
            $query .= 'GROUP BY p.id ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count . ' ';

        $result = $model->db->queryAll($query);
		
    		$query = 'SELECT tax_rate FROM #__eshop_taxes';
    		$taxes = $model->db->queryAll($query);

        $data = array();
        $root = JURI::root();
        foreach ($result AS $res) {
            $this->setCurrencyDetails($res['left_symbol'], $res['right_symbol'], $res['decimal_place'], $res['decimal_symbol'], $res['thousands_separator'], $now, $res['exchanged_value']);
            $r = array(
                'title'             => $res['product_name'],
                'url'               => JRoute::_(EshopRoute::getProductRoute($res['id'], $res['category_id'])),
                'description'       => $res['product_desc'],
                'short_description' => $res['product_short_desc']
            );

            $r['image'] = N2JoomlaImageFallBack::fallback($root, array(
                !empty($res['product_image']) ? 'media/com_eshop/products/' . $res['product_image'] : ''
            ), array($res['product_desc']));

            $reSized = explode('.', $res['product_image']);
            if (count($reSized) == 2 && file_exists(JPATH_ROOT . '/media/com_eshop/products/resized/' . $reSized[0] . '-' . $res['image_thumb_width'] . 'x' . $res['image_thumb_height'] . '.' . $reSized[1])) {
                $r['thumbnail'] = N2ImageHelper::dynamic($root . 'media/com_eshop/products/resized/' . $reSized[0] . '-' . $res['image_thumb_width'] . 'x' . $res['image_thumb_height'] . '.' . $reSized[1]);
            } else {
                $r['thumbnail'] = $r['image'];
            }

            $r += array(
                'price'                                  => $this->createPrice($res['product_price']),
                'price_without_currency_symbol'          => $this->createPrice($res['product_price'], null, null, null, false),
                'discount_price'                         => $this->createPrice($res['price']),
                'discount_price_without_currency_symbol' => $this->createPrice($res['price'], null, null, null, false),
                'id'                                     => $res['id'],
                'product_sku'                            => $res['product_sku'],
                'product_weight'                         => $this->decimals($res['product_weight']),
                'product_length'                         => $this->decimals($res['product_length']),
                'product_width'                          => $this->decimals($res['product_width']),
                'product_height'                         => $this->decimals($res['product_height']),
                'product_shipping_cost'                  => $this->createPrice($res['product_shipping_cost']),
                'hits'                                   => $res['hits'],
                'product_page_title'                     => $res['product_page_title'],
                'product_page_heading'                   => $res['product_page_heading'],
                'tab1_title'                             => $res['tab1_title'],
                'tab1_content'                           => $res['tab1_content'],
                'tab2_title'                             => $res['tab2_title'],
                'tab2_content'                           => $res['tab2_content'],
                'tab3_title'                             => $res['tab3_title'],
                'tab3_content'                           => $res['tab3_content'],
                'tab4_title'                             => $res['tab4_title'],
                'tab4_content'                           => $res['tab4_content'],
                'tab5_title'                             => $res['tab5_title'],
                'tab5_content'                           => $res['tab5_content'],
                'category_name'                          => $res['category_name'],
                'category_desc'                          => $res['category_desc'],
                'category_image'                         => !empty($res['category_image']) ? N2ImageHelper::dynamic($root . 'media/com_eshop/categories/' . $res['category_image']) : '',
                'category_url'                           => JRoute::_(EshopRoute::getCategoryRoute($res['category_id'])),
                'manufacturer_email'                     => $res['manufacturer_email'],
                'manufacturer_url'                       => $res['manufacturer_url'],
                'manufacturer_site_url'                  => 'index.php?option=com_eshop&view=manufacturer&id=' . $res['manufacturer_id'],
                'manufacturer_image'                     => !empty($res['manufacturer_image']) ? N2ImageHelper::dynamic($root . 'media/com_eshop/manufacturers/' . $res['manufacturer_image']) : '',
                'manufacturer_name'                      => $res['manufacturer_name'],
                'manufacturer_desc'                      => $res['manufacturer_desc'],
                'manufacturer_page_title'                => $res['manufacturer_page_title'],
                'manufacturer_page_heading'              => $res['manufacturer_page_heading']
            );

            $r['full_price'] = $r['price'];
            			
      			$j = 1;
      			foreach($taxes AS $tax){
      				$r['price_with_tax' . $j] = $this->createPrice($r['price'] + $r['price'] * $tax['tax_rate'] / 100);
      				$j++;
      			}

            $data[] = $r;
        }

        return $data;
    }

}