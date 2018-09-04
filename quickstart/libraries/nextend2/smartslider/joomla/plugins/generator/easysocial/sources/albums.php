<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorEasySocialAlbums extends N2GeneratorAbstract {

    protected $layout = 'image';


    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementEasySocialGroups($source, 'easysocialgroups', n2_('Groups'), 0, array(
            'isMultiple' => true,
            'size'       => 10
        ));
        new N2ElementEasySocialEvents($source, 'easysocialevents', n2_('Events'), 0, array(
            'isMultiple' => true,
            'size'       => 10
        ));
        new N2ElementEasySocialPages($source, 'easysocialpages', n2_('Pages'), 0, array(
            'isMultiple' => true,
            'size'       => 10
        ));

        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementFilter($limit, 'featured', n2_('Featured'), 0);
        new N2ElementText($limit, 'albumtitle', 'Album title', '*');
        new N2ElementFilter($limit, 'avatarandcover', 'Include avatar and cover images', 0);


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'easysocialorder', n2_('Order'), 'created|*|asc');
        new N2ElementList($order, 'easysocialorder-1', n2_('Field'), '', array(
            'options' => array(
                ''        => n2_('None'),
                'title'   => n2_('Title'),
                'created' => n2_('Creation time'),
                'id'      => 'ID'
            )
        ));

        new N2ElementRadio($order, 'easysocialorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    protected function _getData($count, $startIndex) {

        $model = new N2Model('EasySocial_Albums');

        $groups = array_map('intval', explode('||', $this->data->get('easysocialgroups', '0')));
        $events = array_map('intval', explode('||', $this->data->get('easysocialevents', '0')));
        $pages  = array_map('intval', explode('||', $this->data->get('easysocialpages', '0')));

        if (!in_array('0', $groups) && !in_array('0', $events) && !in_array('0', $pages)) {
            $clusters = array_merge($groups, $events, $pages);
        } else {
            $cluster_helper = array();
            if (!in_array('0', $groups)) {
                $cluster_helper = array_merge($cluster_helper, $groups);
            }
            if (!in_array('0', $events)) {
                $cluster_helper = array_merge($cluster_helper, $events);
            }
            if (!in_array('0', $pages)) {
                $cluster_helper = array_merge($cluster_helper, $pages);
            }
            $clusters = $cluster_helper;
        }

        if (in_array('0', $groups) && in_array('0', $events) && in_array('0', $pages)) {
            $all = "OR uid IN (SELECT id FROM #__social_clusters WHERE cluster_type = 'group' OR cluster_type = 'event' OR cluster_type = 'page')";
        } else if (in_array('0', $groups) && in_array('0', $events)) {
            $all = "OR uid IN (SELECT id FROM #__social_clusters WHERE cluster_type = 'group' OR cluster_type = 'event')";
        } else if (in_array('0', $groups) && in_array('0', $pages)) {
            $all = "OR uid IN (SELECT id FROM #__social_clusters WHERE cluster_type = 'group' OR cluster_type = 'page')";
        } else if (in_array('0', $events) && in_array('0', $pages)) {
            $all = "OR uid IN (SELECT id FROM #__social_clusters WHERE cluster_type = 'event' OR cluster_type = 'page')";
        } else if (in_array('0', $pages)) {
            $all = "OR uid IN (SELECT id FROM #__social_clusters WHERE cluster_type = 'page')";
        } else if (in_array('0', $events)) {
            $all = "OR uid IN (SELECT id FROM #__social_clusters WHERE cluster_type = 'event')";
        } else if (in_array('0', $groups)) {
            $all = "OR uid IN (SELECT id FROM #__social_clusters WHERE cluster_type = 'group')";
        }

        $albumWhere = array("1=1");

        if (!empty($clusters)) {
            $albumWhere[] = "(uid IN (" . implode(',', $clusters) . ") " . $all . ")";
        }

        if ($this->data->get('avatarandcover', '0') == '0') {
            $albumWhere[] = "title <> 'COM_EASYSOCIAL_ALBUMS_PROFILE_AVATAR' AND title <> 'COM_EASYSOCIAL_ALBUMS_PROFILE_COVER'";
        }

        $albumTitle = $this->data->get('albumtitle', '*');
        if ($albumTitle != '*' && !empty($albumTitle)) {
            $albumWhere[] = "title = '" . $albumTitle . "'";
        }

        $where = array(
            "album_id IN (SELECT id FROM #__social_albums WHERE  " . implode(' AND ', $albumWhere) . ")",
            "state = 1"
        );

        switch ($this->data->get('featured', 0)) {
            case 1:
                $where[] = 'featured = 1';
                break;
            case -1:
                $where[] = 'featured = 0';
                break;
        }

        $query = "SELECT
                  id, title
                  FROM #__social_photos
                  WHERE " . implode(' AND ', $where);


        $order = N2Parse::parse($this->data->get('easysocialorder', 'created|*|desc'));
        if ($order[0]) {
            $query .= ' ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= " LIMIT " . $startIndex . ", " . $count;

        $result = $model->db->queryAll($query);
        $data   = array();

        // EasySocial quote: "Prior to ES 2.0, we no longer use square and featured as image variation". This is why the photos are returning thumbnail and large images.
        $photo = ES::table('Photo');
        for ($i = 0; $i < count($result); $i++) {
            $photo->load($result[$i]['id']);
            $r = array(
                'title'     => $result[$i]['title'],
                'image'     => $photo->getSource('original'),
                'thumbnail' => $photo->getSource('thumbnail'),
                'square'    => $photo->getSource('square'),
                'featured'  => $photo->getSource('featured'),
                'large'     => $photo->getSource('large'),
                'stock'     => $photo->getSource('stock')
            );

            $data[] = $r;
        }

        return $data;
    }
}
