<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');


class N2GeneratorJomSocialActivities extends N2GeneratorAbstract {

    protected $layout = 'article';

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
        new N2ElementJomSocialProfiles($source, 'jomsocialprofiles', 'Profiles', 0, array(
            'isMultiple' => true
        ));


        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));

        new N2ElementList($filter, 'acttype', n2_('Type'), 'wall', array(
            'options' => array(
                'wall'              => 'Status',
                'photos'            => 'Photo',
                'videos'            => 'Video',
                'groups.bulletin'   => 'Group announcement',
                'groups.discussion' => 'Group discussion'
            )
        ));
        new N2ElementOnOff($filter, 'hidden', n2_('Show hidden'), 0);
        new N2ElementText($filter, 'userid', n2_('User ID'), '*');

        new N2ElementJomSocialVideoType($filter, 'videotype', 'Video type', 'youtube');

        $featured = new N2ElementGroup($filter, 'featured', n2_('Featured'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementFilter($featured, 'featuredevents', 'Featured events', 0);
        new N2ElementFilter($featured, 'featuredusers', 'Created by featured users', 0);
        new N2ElementFilter($featured, 'featuredgroups', 'Created by featured groups', 0);


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'jomsocialorder', n2_('Order'), 'created|*|desc');
        new N2ElementList($order, 'jomsocialorder-1', n2_('Field'), '', array(
            'options' => array(
                ''           => n2_('None'),
                'created'    => n2_('Creation time'),
                'updated_at' => n2_('Modification time'),
                'id'         => 'ID',
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

        $model = new N2Model('community_events');

        $where = array();

        $events = array_map('intval', explode('||', $this->data->get('jomsocialevents', '0')));
        if (!in_array('0', $events)) {
            $where[] = 'eventid IN (' . implode(',', $events) . ')';
        }

        $groups = array_map('intval', explode('||', $this->data->get('jomsocialgroups', '0')));
        if (!in_array('0', $groups)) {
            $where[] = 'groupid IN (' . implode(',', $groups) . ')';
        }

        $profiles = array_map('intval', explode('||', $this->data->get('jomsocialprofiles', '0')));
        if (!in_array('0', $profiles)) {
            $where[] = 'actor IN (SELECT userid FROM #__community_users WHERE profile_id IN (' . implode(',', $profiles) . '))';
        }

        $type = $this->data->get('acttype', 'wall');
        if ($type == 'wall') {
            $where[] = "(app = 'groups.wall' OR app = 'events.wall')";
        } else {
            $where[] = "app = '" . $type . "'";
        }

        switch ($this->data->get('featuredevents', '-1')) {
            case 1:
                $where[] = "eventid IN (SELECT cid FROM #__community_featured WHERE type = 'events')";
                break;
            case -1:
                $where[] = "eventid NOT IN (SELECT cid FROM #__community_featured WHERE type = 'events')";
                break;
        }

        switch ($this->data->get('featuredusers', '-1')) {
            case 1:
                $where[] = "actor IN (SELECT cid FROM #__community_featured WHERE type = 'users')";
                break;
            case -1:
                $where[] = "actor NOT IN (SELECT cid FROM #__community_featured WHERE type = 'users')";
                break;
        }

        switch ($this->data->get('featuredgroups', '-1')) {
            case 1:
                $where[] = "groupid IN (SELECT cid FROM #__community_featured WHERE type = 'groups')";
                break;
            case -1:
                $where[] = "groupid NOT IN (SELECT cid FROM #__community_featured WHERE type = 'groups')";
                break;
        }

        $userid = $this->data->get('userid', '*');
        if ($userid != '*' && !empty($userid)) {
            $where[] = "actor IN (" . $userid . ")";
        }

        if (!$this->data->get('hidden', '0')) {
            $where[] = "id NOT IN (SELECT activity_id FROM #__community_activities_hide)";
        }

        $query = "SELECT * FROM #__community_activities WHERE " . implode(' AND ', $where) . " ";

        $order = N2Parse::parse($this->data->get('jomsocialorder', 'created|*|desc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $root = N2Uri::getBaseUri();

        $realRoot = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
        $realRoot .= '://' . $_SERVER['SERVER_NAME'];

        $vt = $this->data->get('videotype', 'youtube');
        if ($vt != '') {
            $videoType = "AND type = '" . $vt . "'";
        } else {
            $videoType = "";
        }

        $data = array();
        for ($i = 0; $i < count($result); $i++) {

            $r = array();

            if (!empty($result[$i]['title'])) {
                $r['title'] = $result[$i]['title'];
            }

            if (!empty($result[$i]['content'])) {
                $r['description'] = $result[$i]['content'];
            }

            switch ($type) {
                case 'photos':
                    $params = json_decode($result[$i]['params']);
                    $query  = "SELECT image, thumbnail, original FROM #__community_photos WHERE id = " . $params->photoid . " LIMIT 1";
                    $image  = $model->db->queryAll($query);
                    $r += array(
                        'image'     => N2ImageHelper::dynamic($root . '/' . $image[0]['image']),
                        'thumbnail' => N2ImageHelper::dynamic($root . '/' . $image[0]['thumbnail']),
                        'original'  => $root . '/' . $image[0]['original'],
                        'album_url' => $realRoot . $params->multiUrl,
                        'photo_url' => $realRoot . $params->photo_url
                    );
                    break;
                case 'videos':
                    $params = json_decode($result[$i]['params']);
                    $query  = "SELECT path, title, thumb, video_id FROM #__community_videos WHERE id = " . $result[$i]['cid'] . " " . $videoType . " LIMIT 1";
                    $video  = $model->db->queryAll($query);
                    if (isset($video[0])) {
                        $r += array(
                            'video_link'  => $video[0]['path'],
                            'video_title' => $video[0]['title'],
                            'image'       => N2ImageHelper::dynamic($root . '/' . $video[0]['thumb']),
                            'video_id'    => $video[0]['video_id']
                        );
                    }
                    $r['video_url'] = $root . '/' . $params->video_url;
                    break;
                case 'groups.bulletin':
                    $params                = json_decode($result[$i]['params']);
                    $r['announcement_url'] = $root . '/' . $params->group_url;
                    break;
                case 'groups.discussion':
                    $params = json_decode($result[$i]['params']);
                    $r += array(
                        'group_url' => $root . '/' . $params->group_url,
                        'topic_url' => $root . '/' . $params->topic_url
                    );
                    break;
                default:
                    break;
            }

            $r['created'] = $result[$i]['created'];

            if (!empty($result[$i]['location'])) {
                $r['location'] = $result[$i]['location'];
            }
            if (!empty($result[$i]['latitude'])) {
                $r['latitude'] = $result[$i]['latitude'];
            }
            if (!empty($result[$i]['longitude'])) {
                $r['longitude'] = $result[$i]['longitude'];
            }

            $r['id'] = $result[$i]['id'];

            if (($type == 'videos' && isset($video[0]['path'])) || ($type != 'videos')) {
                $data[] = $r;
            }
        }

        return $data;
    }
}