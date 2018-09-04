<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');
require_once(dirname(__FILE__) . '/../../../../../../../../components/com_eventbooking/helper/helper.php');
require_once(dirname(__FILE__) . '/../../../../../../../../components/com_eventbooking/helper/route.php');

class N2GeneratorEventsBookingEvents extends N2GeneratorAbstract {

    protected $layout = 'event';

    private function formatDate($datetime, $dateOrTime, $format) {
        if ($dateOrTime == 1 || $datetime != '0000-00-00 00:00:00') {
            return date($format, strtotime($datetime));
        } else {
            return '';
        }
    }


    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementEventsBookingCategories($source, 'sourcecategories', n2_('Categories'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementEventsBookingLocations($source, 'sourcelocations', n2_('Locations'), 0, array(
            'isMultiple' => true
        ));

        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementFilter($limit, 'started', n2_('Started'), 0);
        new N2ElementFilter($limit, 'ended', n2_('Ended'), -1);
        new N2ElementFilter($limit, 'published', n2_('Published'), 1);
        new N2ElementFilter($limit, 'featured', n2_('Featured'), 0);
		new N2ElementList($limit, 'recurring', n2_('Recurring'), '0', array(
            'options' => array(
                '0' => n2_('All'),
                '1' => n2_('All, but from recurring ones only parent events'),
                '2' => n2_('Only recurring events'),
                '3' => n2_('Only recurring event parents'),
                '4' => n2_('Only not recurring events')
            )
        ));
		
		
        $variables = new N2ElementGroup($filter, 'variable', n2_('Variables'), array(
            'rowClass' => 'n2-expert'
        ));
		new N2ElementText($variables, 'dateformat', n2_('Date format'), n2_('m-d-Y'));
        new N2ElementText($variables, 'timeformat', n2_('Time format'), 'G:i');
		new N2ElementMenuItems($variables, 'itemid', n2_('Menu item (item ID) for url'), 0);
		
        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'eventsbookingorder', n2_('Order'), 'event_date|*|asc');
        new N2ElementList($order, 'eventsbookingorder-1', n2_('Field'), '', array(
            'options' => array(
                ''                           => n2_('None'),
                'event_date'                 => n2_('Start date'),
                'event_end_date'             => n2_('End date'),
                'id'                         => n2_('ID'),
                'title'                      => n2_('Title'),
                'individual_price'           => n2_('Price'),
                'discount'                   => n2_('Discount'),
                'registration_start_date'    => n2_('Registration start date'),
                'cut_off_date'               => n2_('Cut off date'),
                'cancel_before_date'         => n2_('Cancel before date'),
                'publish_up'                 => n2_('Publish up date'),
                'publish_down'               => n2_('Publish down date'),
                'early_bird_discount_date'   => n2_('Early bird discount date'),
                'early_bird_discount_amount' => n2_('Early bird discount amount'),
                'late_fee_date'              => n2_('Late fee date'),
                'recurring_end_date'         => n2_('Recurring end date'),
                'max_end_date'               => n2_('Max end date')
            )
        ));

        new N2ElementRadio($order, 'eventsbookingorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    protected function _getData($count, $startIndex) {
    	$dateFormat = $this->data->get('dateformat', 'Y-m-d');		
        if (empty($dateFormat)) {
            $dateFormat = 'Y-m-d';
        }

        $timeFormat = $this->data->get('timeformat', 'H:i:s');
        if (empty($timeFormat)) {
            $timeFormat = 'H:i:s';
        }

        $itemId = $this->data->get('itemid', '0');
        $model  = new N2Model('jevents_vevent');

        $where = array();

        $categories = array_map('intval', explode('||', $this->data->get('sourcecategories', '')));
        if (!in_array('0', $categories)) {
            $where[] = ' id IN (SELECT event_id FROM #__eb_event_categories WHERE category_id IN (' . implode(', ', $categories) . '))';
        }

        $locations = array_map('intval', explode('||', $this->data->get('sourcelocations', '')));
        if (!in_array('0', $locations)) {
            $where[] = ' location_id IN(' . implode(', ', $locations) . ')';
        }

        $today = date('Y-m-d h:i:s', time());

        switch ($this->data->get('started', '0')) {
            case 1:
                $where[] = " event_date < '" . $today . "'";
                break;
            case -1:
                $where[] = " event_date >= '" . $today . "'";
                break;
        }

        switch ($this->data->get('ended', '-1')) {
            case 1:
                $where[] = " event_end_date < '" . $today . "'";
                break;
            case -1:
                $where[] = " event_end_date >= '" . $today . "'";
                break;
        }

        switch ($this->data->get('recurring', '0')) {
            case 0:
                break;
            case 1:
                $where[] = " parent_id = 0";
                break;
            case 2:
                $where[] = " ((recurring_type > 0) || (parent_id > 0))";
                break;
            case 3:
                $where[] = " recurring_type > 0";
                break;
            case 4:
                $where[] = " recurring_frequency is NULL";
                break;
        }

        switch ($this->data->get('published', '1')) {
            case 0:
                break;
            case 1:
                $where[] = " published = 1";
                break;
            case -1:
                $where[] = " published = 0";
                break;
        }

        switch ($this->data->get('featured', '1')) {
            case 0:
                break;
            case 1:
                $where[] = " featured = 1";
                break;
            case -1:
                $where[] = " featured = 0";
                break;
        }

        $query = 'SELECT * FROM #__eb_events';
        if (!empty($where)) {
            $query .= ' WHERE' . implode(' AND ', $where);
        }

        $order = N2Parse::parse($this->data->get('eventsbookingorder', 'event_date|*|asc'));
        if ($order[0]) {
            $query .= ' ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= ' LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);
        $data   = array();
        $root   = N2Uri::getBaseUri();
        $config = EventbookingHelper::getConfig();
        foreach ($result AS $res) {
            $r = array(
                'title'             => $res['title'],
                'description'       => $res['description'],
                'short_description' => $res['short_description']
            );

            $r['image'] = N2JoomlaImageFallBack::fallback($root . '/', array(
                !empty($res['thumb']) ? 'media/com_eventbooking/images/' . $res['thumb'] : ''
            ), array(
                $res['description'],
                $res['short_description']
            ));

            $r['thumbnail'] = N2JoomlaImageFallBack::fallback($root . '/', array(
                !empty($res['thumb']) ? 'media/com_eventbooking/images/thumb/' . $res['thumb'] : '',
                $r['image']
            ));

            $r['url'] = JRoute::_(EventbookingHelperRoute::getEventRoute($res['id'], 0, $itemId), false);
            $r += array(
                'start_date'                             => $this->formatDate($res['event_date'], 0, $dateFormat),
                'start_time'                             => $this->formatDate($res['event_date'], 1, $timeFormat),
                'end_date'                               => $this->formatDate($res['event_end_date'], 0, $dateFormat),
                'end_time'                               => $this->formatDate($res['event_end_date'], 1, $timeFormat),
                'price'                                  => EventbookingHelper::formatCurrency($res['individual_price'], $config, $res['currency_symbol']),
                'discount'                               => EventbookingHelper::formatCurrency($res['discount'], $config, $res['currency_symbol']),
                'unformatted_price'                      => $res['individual_price'],
                'unformatted_discount'                   => $res['discount'],
                'tax_rate'                               => $res['tax_rate'],
                'price_with_tax'                         => EventbookingHelper::formatCurrency(round($res['individual_price'] * (1 + $res['tax_rate'] / 100), 2), $config, $res['currency_symbol']),
                'unformatted_price_with_tax'             => round($res['individual_price'] * (1 + $res['tax_rate'] / 100), 2),
                'early_bird_discount_date'               => $this->formatDate($res['early_bird_discount_date'], 0, $dateFormat),
                'early_bird_discount_amount'             => EventbookingHelper::formatCurrency($res['early_bird_discount_amount'], $config, $res['currency_symbol']),
                'unformatted_early_bird_discount_amount' => $res['early_bird_discount_amount'],
                'cut_off_date'                           => $this->formatDate($res['cut_off_date'], 0, $dateFormat),
                'cancel_before_date'                     => $this->formatDate($res['cancel_before_date'], 0, $dateFormat),
                'recurring_end_date'                     => $this->formatDate($res['recurring_end_date'], 0, $dateFormat),
                'registration_start_date'                => $this->formatDate($res['registration_start_date'], 0, $dateFormat)
            );
            $data[] = $r;
        }
        return $data;
    }

}