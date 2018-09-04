<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorJomSocialVideos extends N2GeneratorAbstract {

    protected $layout = 'youtube';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementJomSocialEvents($source, 'jomsocialevents', n2_('Events'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementJomSocialGroups($source, 'jomsocialgroups', n2_('Groups'), 0, array(
            'isMultiple' => true
        ));


        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementJomSocialVideoType($filter, 'videotype', n2_('Type'), 'youtube');
        new N2ElementNumber($filter, 'userid', n2_('User ID'), '*');
        new N2ElementFilter($limit, 'featured', 'Featured', 0);


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'jomsocialorder', n2_('Order'), 'created|*|desc');
        new N2ElementList($order, 'jomsocialorder-1', n2_('Field'), '', array(
            'options' => array(
                ''        => n2_('None'),
                'title'   => n2_('Title'),
                'created' => n2_('Creation time'),
                'hits'    => n2_('Hits'),
                'id'      => 'ID',
            )
        ));

        new N2ElementRadio($order, 'jomsocialorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    protected function _getData($count, $startIndex) {

        require_once(JPATH_SITE . '/components/com_community/router.php');

        $model = new N2Model('community_videos');

        $where = array(
            "published = 1",
            "type = '" . $this->data->get('videotype', 'youtube') . "'"
        );

        $group  = array_map('intval', explode('||', $this->data->get('jomsocialgroups', '0')));
        $events = array_map('intval', explode('||', $this->data->get('jomsocialevents', '0')));

        if (!in_array('0', $group) && !in_array('0', $events)) {
            $where[] = '(groupid IN (' . implode(',', $group) . ') OR eventid IN (' . implode(',', $events) . '))';
        } else if (!in_array('0', $group)) {
            $where[] = 'groupid IN (' . implode(',', $group) . ')';
        } else if (!in_array('0', $events)) {
            $where[] = 'eventid IN (' . implode(',', $events) . ')';
        }

        $userID = $this->data->get('userid', '*');
        if ($userID != '*' && !empty($userID)) {
            $where[] = 'creator IN (' . $userID . ')';
        }

        switch ($this->data->get('featured', '-1')) {
            case 1:
                $where[] = "featured = 1";
                break;
            case -1:
                $where[] = "featured = 0";
                break;
        }

        $query = "SELECT * FROM #__community_videos WHERE " . implode(' AND ', $where) . " ";

        $order = N2Parse::parse($this->data->get('jomsocialorder', 'created|*|desc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $root = N2Uri::getBaseUri();

        $data = array();
        for ($i = 0; $i < count($result); $i++) {
            $image  = N2ImageHelper::dynamic($root . '/' . $result[$i]['thumb']);
            $r      = array(
                'title'      => $result[$i]['title'],
                'video_path' => $result[$i]['path'],
                'video_id'   => $result[$i]['video_id'],
                'image'      => $image,
                'thumbnail'  => $image,
                'id'         => $result[$i]['id']
            );
            $data[] = $r;
        }

        return $data;
    }
}
