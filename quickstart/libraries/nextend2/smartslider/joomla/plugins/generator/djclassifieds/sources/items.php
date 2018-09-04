<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorDJClassifiedsItems extends N2GeneratorAbstract {

    protected $layout = 'image';


    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementDJClassifiedsCategories($source, 'categories', n2_('Categories'), 0, array(
            'isMultiple' => true,
            'size'       => 10
        ));

        new N2ElementDJClassifiedsLocations($source, 'locations', n2_('Locations'), 0, array(
            'isMultiple' => true,
            'size'       => 10
        ));

        new N2ElementDJClassifiedsTypes($source, 'types', n2_('Types'), 0, array(
            'isMultiple' => true,
            'size'       => 10
        ));


        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));

        new N2ElementFilter($limit, 'expired', n2_('Expired'), '-1');

        new N2ElementFilter($limit, 'started', n2_('Started'), '1');

        new N2ElementFilter($limit, 'auction', n2_('Auction'), 0);

        new N2ElementFilter($limit, 'negotiable', n2_('Negotiable price'), 0);

        new N2ElementFilter($limit, 'blocked', n2_('Blocked by User'), '-1');

        new N2ElementFilter($limit, 'paid', n2_('Paid'), 0);

        new N2ElementFilter($limit, 'buynow', n2_('Buynow'), 0);

        $url = new N2ElementGroup($filter, 'url', n2_('URL'), array(
            'rowClass' => 'n2-expert',
            'tip'      => "Your url will point to the item within this menu item. You will only get a good result, if you will pick one of your DJ Classifields menu items or leave it on 'Default'."
        ));

        new N2ElementMenuItems($url, 'itemid', n2_('Menu item (item ID)'), 0);

        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'djclassifiedsorder', n2_('Order'), 'date_start|*|asc');
        new N2ElementList($order, 'djclassifiedsorder-1', n2_('Field'), '', array(
            'options' => array(
                ''             => n2_('None'),
                'i.name'       => n2_('Name'),
                'i.date_start' => n2_('Start date'),
                'i.date_exp'   => n2_('Expiration date'),
                'ABS(i.price)' => n2_('Price'),
                'i.id'         => 'ID'
            )
        ));

        new N2ElementRadio($order, 'djclassifiedsorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }
	
	protected function fileExists($path, $root = JPATH_SITE){
		$file = $root . $path;
		if (N2Filesystem::fileexists($file)) {
			return N2ImageHelper::dynamic(N2Uri::pathToUri($file));
		} else {
			return '';
		}
	}

    protected function _getData($count, $startIndex) {
        require_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_djclassifieds' . DS . 'lib' . DS . 'djseo.php');

        $model = new N2Model('DJClassifieds_Items');

        $categories = array_map('intval', explode('||', $this->data->get('categories', 0)));
        $locations  = array_map('intval', explode('||', $this->data->get('locations', 0)));
        $types      = array_map('intval', explode('||', $this->data->get('types', 0)));

        $where = array(
            "i.published = 1 "
        );

        if (!in_array(0, $categories) && count($categories) > 0) {
            $where[] = "i.cat_id IN (" . implode(',', $categories) . ")";
        }

        if (!in_array(0, $locations) && count($locations) > 0) {
            $where[] = "i.region_id IN (" . implode(',', $locations) . ")";
        }

        if (!in_array(0, $types) && count($types) > 0) {
            $where[] = "i.type_id IN (" . implode(',', $types) . ")";
        }

        $today = date('Y-m-d h:i:s', time());

        switch ($this->data->get('expired', '-1')) {
            case 1:
                $where[] = "i.date_exp <= '" . $today . "'";
                break;
            case -1:
                $where[] = "i.date_exp > '" . $today . "'";
                break;
        }

        switch ($this->data->get('started', '1')) {
            case 1:
                $where[] = "i.date_start <= '" . $today . "'";
                break;
            case -1:
                $where[] = "i.date_start > '" . $today . "'";
                break;
        }

        switch ($this->data->get('negotiable', 0)) {
            case 1:
                $where[] = "i.price_negotiable = 1 ";
                break;
            case -1:
                $where[] = "i.price_negotiable = 0 ";
                break;
        }

        switch ($this->data->get('blocked', 0)) {
            case 1:
                $where[] = "i.blocked = 1 ";
                break;
            case -1:
                $where[] = "i.blocked = 0 ";
                break;
        }

        switch ($this->data->get('paid', 0)) {
            case 1:
                $where[] = "i.payed = 1 ";
                break;
            case -1:
                $where[] = "i.payed = 0 ";
                break;
        }

        switch ($this->data->get('buynow', 0)) {
            case 1:
                $where[] = "i.buynow = 1 ";
                break;
            case -1:
                $where[] = "i.buynow = 0 ";
                break;
        }

        switch ($this->data->get('auction', 0)) {
            case 1:
                $where[] = "i.auction = 1 ";
                break;
            case -1:
                $where[] = "i.auction = 0 ";
                break;
        }

        $query = "SELECT 
                    i.id, i.cat_id, i.name, i.alias, i.description, i.intro_desc, i.date_start, i.date_exp, 
                    i.price, i.contact, i.address, i.post_code, i.video, i.website, i.currency, 
                    i.metakey, i.metadesc, i.email, i.bid_min, i.bid_max, i.price_reserve, i.price_start, 
                    im.path, im.name AS image_name, im.ext, im.caption,
                    c.name AS category_name, c.alias AS category_alias,
                    r.name AS region_name, r.id AS region_id 
                    FROM #__djcf_items AS i 
                    LEFT JOIN #__djcf_images AS im ON i.id = im.item_id AND im.type='item'  
                    LEFT JOIN #__djcf_categories AS c ON i.cat_id = c.id  
                    LEFT JOIN #__djcf_regions AS r ON i.region_id = r.id  
                    WHERE " . implode(' AND ', $where);

        $order = N2Parse::parse($this->data->get('djclassifiedsorder', 'date_start|*|asc'));
        if ($order[0]) {
            $query .= ' ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= ' LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        if ($this->data->get('itemid', 0)) {
            $itemid = '&Itemid=' . $this->data->get('itemid', 0);
        } else {
            $itemid = '';
        }

        $data = array();
        for ($i = 0; $i < count($result); $i++) {
            $r = array(
                'name'        => $result[$i]['name'],
                'title'       => $result[$i]['name'],
                'description' => $result[$i]['description'],
                'intro_desc'  => $result[$i]['intro_desc'],
                'url'         => JRoute::_(DJClassifiedsSEO::getItemRoute($result[$i]['id'], $result[$i]['cat_id'], $result[$i]['region_id']) . $itemid, false) );

            if (!empty($result[$i]['image_name'])) {
                $r += array(
                    'image_original'     => $this->fileExists($result[$i]['path'] . $result[$i]['image_name'] . '.' .  $result[$i]['ext']),
                    'image_ths' => $this->fileExists($result[$i]['path'] . $result[$i]['image_name'] . '_ths.' . $result[$i]['ext']),
                    'image_thm' => $this->fileExists($result[$i]['path'] . $result[$i]['image_name'] . '_thm.' . $result[$i]['ext']),
                    'image_thb' => $this->fileExists($result[$i]['path'] . $result[$i]['image_name'] . '_thb.' . $result[$i]['ext']),
                );
				
				$r['image'] = N2JoomlaImageFallBack::fallback(
						JURI::root(), 
						array(
							ltrim($r['image_original'], '$/'), 
							ltrim($r['image_thb'], '$/'), 
							ltrim($r['image_thm'], '$/'), 
							ltrim($r['image_ths'], '$/')
						),
						array(
							$result[$i]['description'], 
							$result[$i]['intro_desc']
						)
				);
            }

            $r += array(
                'date_start'    => $result[$i]['date_start'],
                'date_exp'      => $result[$i]['date_exp'],
                'price'         => $result[$i]['price'],
                'currency'      => $result[$i]['currency'],
                'contact'       => $result[$i]['contact'],
                'address'       => $result[$i]['address'],
                'post_code'     => $result[$i]['post_code'],
                'video'         => $result[$i]['video'],
                'website'       => $result[$i]['website'],
                'metakey'       => $result[$i]['metakey'],
                'metadesc'      => $result[$i]['metadesc'],
                'email'         => $result[$i]['email'],
                'bid_min'       => $result[$i]['bid_min'],
                'bid_max'       => $result[$i]['bid_max'],
                'price_reserve' => $result[$i]['price_reserve'],
                'price_start'   => $result[$i]['price_start'],
                'image_caption' => $result[$i]['caption'],
                'id'            => $result[$i]['id'],
                'cat_id'        => $result[$i]['cat_id'],
            );

            $data[] = $r;
        }

        return $data;
    }
}