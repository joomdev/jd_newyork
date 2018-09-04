<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');


class N2GeneratorRSEventsProEvents extends N2GeneratorAbstract {

    protected $layout = 'event';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementRSEventsProCategories($source, 'sourcecategories', n2_('Category'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementRSEventsProGroups($source, 'sourcegroups', n2_('Group'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementRSEventsProLocations($source, 'sourcelocations', n2_('Location'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementRSEventsProTags($source, 'sourcetags', n2_('Tag'), 0, array(
            'isMultiple' => true
        ));


        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementFilter($limit, 'started', n2_('Started'), 0);
        new N2ElementFilter($limit, 'ended', n2_('Ended'), -1);
        new N2ElementFilter($limit, 'featured', n2_('Featured'), 0);
        new N2ElementFilter($limit, 'allday', n2_('All day event'), 0);
        new N2ElementFilter($limit, 'recurring', n2_('Recurring event'), 0);


        $date = new N2ElementGroup($filter, 'date', n2_('Date'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementText($date, 'rseventsprodate', n2_('Date format'), n2_('m-d-Y'));
        new N2ElementText($date, 'rseventsprotime', n2_('Time format'), 'G:i');
        new N2ElementTextarea($date, 'rseventstranslatedate', n2_('Translate date and time'), 'January->January||February->February||March->March', array(
            'fieldStyle' => 'width:300px;height: 100px;'
        ));
        new N2ElementText($date, 'rseventsoffset', 'Offset value', '', array(
            'tip' => 'Timezone offset in hours. For example: +2 or -7. If you leave it empty, Joomla\'s System -> Global Configuration -> Server -> Server Time Zone setting will be used.'
        ));


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'rseventsproorder', n2_('Order'), 'start|*|desc');
        new N2ElementList($order, 'rseventsproorder-1', n2_('Field'), '', array(
            'options' => array(
                ''        => n2_('None'),
                'start'   => n2_('Start date'),
                'end'     => n2_('End date'),
                'created' => n2_('Creation date'),
                'name'    => n2_('Title'),
                'hits'    => n2_('Hits'),
                'id'      => 'ID'
            )
        ));

        new N2ElementRadio($order, 'rseventsproorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    private function translate($from, $translate) {
        if (!empty($translate) && !empty($from)) {
            foreach ($translate AS $key => $value) {
                $from = str_replace($key, $value, $from);
            }
        }

        return $from;
    }

    private function formatDate($datetime, $format = 'Y-m-d', $strtotime = true) {
        if ($datetime != '0000-00-00 00:00:00') {
            if ($strtotime) {
                $datetime = strtotime($datetime);
            }

            return date($format, $datetime);
        } else {
            return '';
        }
    }

    protected function _getData($count, $startIndex) {
        require_once(JPATH_SITE . '/components/com_rseventspro/helpers/rseventspro.php');
        require_once(JPATH_SITE . '/components/com_rseventspro/helpers/route.php');

        $categories = array_map('intval', explode('||', $this->data->get('sourcecategories', '')));
        $groups     = array_map('intval', explode('||', $this->data->get('sourcegroups', '')));
        $tags       = array_map('intval', explode('||', $this->data->get('sourcetags', '')));
        $locations  = array_map('intval', explode('||', $this->data->get('sourcelocations', '')));

        $model = new N2Model('rseventspro_events');

        $where = array('re.published <> 0');

        if (!in_array('0', $categories)) {
            $where[] = "re.id IN (SELECT ide FROM #__rseventspro_taxonomy WHERE id IN (" . implode(', ', $categories) . ") AND type = 'category')";
        }

        if (!in_array('0', $groups)) {
            $where[] = "re.id IN (SELECT ide FROM #__rseventspro_taxonomy WHERE id IN (" . implode(', ', $groups) . ") AND type = 'groups')";
        }

        if (!in_array('0', $tags)) {
            $where[] = "re.id IN (SELECT ide FROM #__rseventspro_taxonomy WHERE id IN (" . implode(', ', $tags) . ") AND type = 'tag')";
        }

        if (!in_array('0', $locations)) {
            $where[] = "re.location IN (" . implode(', ', $locations) . ")";
        }
        if (function_exists('rseventsproHelper::showdate')) {
            $today = rseventsproHelper::showdate("now", 'Y-m-d h:i:s');
        } else {
            $today = date('Y-m-d h:i:s', time());
        }

        $config   = JFactory::getConfig();
        $timezone = new DateTimeZone($config->get('offset'));
        $offset   = $timezone->getOffset(new DateTime);

        if ($this->data->get('rseventsoffset', '') !== '') {
            $offset = intval($this->data->get('rseventsoffset', 0)) * 3600;
        }

        switch ($this->data->get('started', '0')) {
            case 1:
                $where[] = "DATE_ADD(re.start, INTERVAL " . $offset . " SECOND) < '" . $today . "'";
                break;
            case -1:
                $where[] = "DATE_ADD(re.start, INTERVAL " . $offset . " SECOND) >= '" . $today . "'";
                break;
        }

        switch ($this->data->get('ended', '-1')) {
            case 1:
                $where[] = "((DATE_ADD(re.end, INTERVAL " . $offset . " SECOND) < '" . $today . "' AND re.allday = 0) OR (DATE_ADD(re.start , INTERVAL " . $offset . " SECOND)< '" . $today . "' AND re.allday = 1))";
                break;
            case -1:
                $where[] = "((DATE_ADD(re.end, INTERVAL " . $offset . " SECOND) >= '" . $today . "' AND re.allday = 0) OR (DATE_ADD(re.start, INTERVAL " . $offset . " SECOND) >= '" . $today . "' AND re.allday = 1))";
                break;
        }

        switch ($this->data->get('allday', '0')) {
            case 1:
                $where[] = "re.allday = 1";
                break;
            case -1:
                $where[] = "re.allday = 0";
                break;
        }

        switch ($this->data->get('recurring', '0')) {
            case 1:
                $where[] = "re.recurring = 1";
                break;
            case -1:
                $where[] = "re.recurring = 0";
                break;
        }

        $query = 'SELECT
        re.start, re.end, re.id, re.name, re.description, re.created, re.URL, re.email, re.phone, re.metaname, re.metakeywords, re.metadescription, re.hits, re.icon, 
        rl.name as loc_name, rl.url as loc_url, rl.address, rl.description AS loc_description, rl.coordinates
        FROM #__rseventspro_events AS re
        LEFT JOIN #__rseventspro_locations AS rl ON re.location = rl.id
        WHERE ' . implode(' AND ', $where) . ' ';

        $order = N2Parse::parse($this->data->get('rseventsproorder', 'start|*|asc'));
        if ($order[0]) {
            $query .= 'ORDER BY re.' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $data       = array();
        $root       = N2Uri::getBaseUri();
        $dateFormat = $this->data->get('rseventsprodate', 'm-d-Y');
        $timeFormat = $this->data->get('rseventsprotime', 'G:i');

        $translateDate  = $this->data->get('rseventstranslatedate', '');
        $translateValue = explode('||', $translateDate);
        $translate      = array();
        if ($translateDate != 'January->January||February->February||March->March' && !empty($translateValue)) {
            foreach ($translateValue AS $tv) {
                $translateArray = explode('->', $tv);
                if (!empty($translateArray) && count($translateArray) == 2) {
                    $translate[$translateArray[0]] = $translateArray[1];
                }
            }
        }

        $config = rseventsproHelper::getConfig();
        foreach ($result AS $res) {
            $r = array(
                'title'       => $res['name'],
                'description' => $res['description']
            );

            if (isset($res['icon'])) {
                $res['icon'] = 'components/com_rseventspro/assets/images/events/' . $res['icon'];
            } else {
                $res['icon'] = '';
            }

            $r['image'] = $r['thumbnail'] = N2JoomlaImageFallBack::fallback($root . "/", array(
                @$res['icon']
            ), array(
                @$res['description']
            ));

            $r['icon_small_width'] = rseventsproHelper::thumb($res['id'], $config->icon_small_width);
            $r['icon_big_width']   = rseventsproHelper::thumb($res['id'], $config->icon_big_width);

            if(isset($r['icon_small_width'])){
                $r['thumbnail'] = $r['icon_small_width'];
            } else {
                $r['thumbnail'] = $r['image'];
            }

            if ($res['start'] != '0000-00-00 00:00:00') {
                $res['start'] = $this->formatDate(strtotime($res['start']) + $offset, 'Y-m-d H:i:s', false);
            }
            if ($res['end'] != '0000-00-00 00:00:00') {
                $res['end'] = $this->formatDate(strtotime($res['end']) + $offset, 'Y-m-d H:i:s', false);
            }
            $res['created'] = $this->formatDate(strtotime($res['created']) + $offset, 'Y-m-d H:i:s', false);
            if (function_exists('rseventsproHelper::showdate')) {
                $r += array(
                    'start_date' => $this->translate(rseventsproHelper::showdate($res['start'], $dateFormat), $translate),
                    'start_time' => $this->translate(rseventsproHelper::showdate($res['start'], $timeFormat), $translate),
                    'end_date'   => $this->translate(rseventsproHelper::showdate($res['end'], $dateFormat), $translate),
                    'end_time'   => $this->translate(rseventsproHelper::showdate($res['end'], $timeFormat), $translate)
                );
            } else {
                $r += array(
                    'start_date' => $this->translate($this->formatDate($res['start'], $dateFormat), $translate),
                    'start_time' => $this->translate($this->formatDate($res['start'], $timeFormat), $translate),
                    'end_date'   => $this->translate($this->formatDate($res['end'], $dateFormat), $translate),
                    'end_time'   => $this->translate($this->formatDate($res['end'], $timeFormat), $translate)
                );
            }
            $r += array(
                'url'                  => rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id=' . rseventsproHelper::sef($res['id'], $res['name']), true, RseventsproHelperRoute::getEventsItemid()),
                'created'              => $res['created'],
                'website'              => $res['URL'],
                'email'                => $res['email'],
                'phone'                => $res['phone'],
                'metaname'             => $res['metaname'],
                'metakeywords'         => $res['metakeywords'],
                'metadescription'      => $res['metadescription'],
                'hits'                 => $res['hits'],
                'id'                   => $res['id'],
                'location_name'        => $res['loc_name'],
                'location_url'         => $res['loc_url'],
                'location_address'     => $res['address'],
                'location_description' => $res['loc_description']
            );

            $coordinates = explode(',', $res['coordinates']);
            if (count($coordinates) == 2) {
                $r += array(
                    'location_coordinates_lat'  => $coordinates[0],
                    'location_coordinates_long' => $coordinates[1]
                );
            }

            $data[] = $r;
        }

        return $data;
    }
}