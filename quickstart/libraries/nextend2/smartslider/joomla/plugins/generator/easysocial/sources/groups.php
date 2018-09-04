<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');


class N2GeneratorEasySocialGroups extends N2GeneratorAbstract {

    protected $layout = 'article';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementEasySocialCategories($source, 'easysocialcategories', n2_('Categories'), 0, array(
            'isMultiple' => true,
            'size'       => 10
        ));

        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementFilter($limit, 'featured', n2_('Featured'), 0);
        new N2ElementList($limit, 'grouptype', n2_('Type'), 0, array(
            'options' => array(
                '0' => n2_('All'),
                '1' => n2_('Open'),
                '2' => n2_('Closed'),
                '3' => n2_('Invite only')
            )
        ));


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'easysocialorder', n2_('Order'), 'a.created|*|asc');
        new N2ElementList($order, 'easysocialorder-1', n2_('Field'), '', array(
            'options' => array(
                ''          => n2_('None'),
                'a.title'   => n2_('Title'),
                'a.created' => n2_('Creation time'),
                'a.id'      => 'ID'
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

        $model = new N2Model('EasySocial_Groups');

        $where = array(
            "a.parent_id = 0",
            "a.cluster_type = 'group'",
            "a.state = '1'"
        );

        $category = array_map('intval', explode('||', $this->data->get('easysocialcategories', '')));

        if (!in_array('0', $category)) {
            $where[] = 'a.category_id IN (' . implode(',', $category) . ')';
        }

        switch ($this->data->get('featured', 0)) {
            case 1:
                $where[] = 'a.featured = 1';
                break;
            case -1:
                $where[] = 'a.featured = 0';
                break;
        }

        $type = $this->data->get('grouptype', 0);
        if ($type != 0) {
            $where[] = 'a.type = ' . $type;
        }

        $location = $this->data->get('location', '*');
        if ($location != '*' && !empty($location)) {
            $where[] = "a.address = '" . $location . "'";
        }

        $query = "SELECT
                  a.id, a.title, a.description, a.created, a.alias, a.category_id,
                  (SELECT photo_id FROM #__social_covers WHERE uid = a.id and type='group' LIMIT 1) AS photo_id
                  FROM #__social_clusters AS a
                  WHERE " . implode(' AND ', $where) . "  ";

        $order = N2Parse::parse($this->data->get('easysocialorder', 'a.created|*|desc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count . ' ';

        $result = $model->db->queryAll($query);

        if (!class_exists('FRoute')) {
            if (file_exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysocial' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'easysocial.php')) {
                require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysocial' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'easysocial.php');
            }
            require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysocial' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'router.php');
        }

        $urlOptions = array(
            'layout'   => 'item',
            'external' => false,
            'sef'      => true
        );

        $avatar = ES::table('Avatar');
        $photo  = ES::table('Photo');
        $data   = array();
        for ($i = 0; $i < count($result); $i++) {
            $urlOptions['id'] = $result[$i]['id'];
            $photo->load($result[$i]['photo_id']);
            $avatar->load(array(
                'uid'  => $result[$i]['id'],
                'type' => 'group'
            ));

            $r = array(
                'title'       => $result[$i]['title'],
                'description' => $result[$i]['description']
            );

            $photoLarge  = $photo->getSource('large');
            $avatarLarge = $avatar->getSource('large');
            $r['image']  = $r['thumbnail'] = N2JoomlaImageFallBack::fallback('', array(
                @$photo->getSource('original'),
                @$photoLarge,
                @$avatarLarge
            ), array());

            $r += array(
                'thumbnail'           => $photo->getSource('thumbnail'),
                'square_image'        => $photo->getSource('square'),
                'featured_image'      => $photo->getSource('featured'),
                'large_image'         => $photoLarge,
                'stock_image'         => $photo->getSource('stock'),
                'avatar_small_image'  => $avatar->getSource('small'),
                'avatar_medium_image' => $avatar->getSource('medium'),
                'avatar_square_image' => $avatar->getSource('square'),
                'avatar_large_image'  => $avatarLarge,
                'url'                 => FRoute::groups($urlOptions, true),
                'creation_time'       => $result[$i]['created'],
                'alias'               => $result[$i]['alias'],
                'category_id'         => $result[$i]['category_id'],
                'id'                  => $result[$i]['id']
            );

            $data[] = $r;
        }

        return $data;
    }
}