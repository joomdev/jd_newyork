<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');


class N2GeneratorJomSocialGroups extends N2GeneratorAbstract {

    protected $layout = 'article';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementJomSocialGroupCategories($source, 'jomsocialgroupcategories', n2_('Category'), 0, array(
            'isMultiple' => true
        ));


        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementFilter($limit, 'featured', 'Featured', 0);

        new N2ElementText($limit, 'grouptype', n2_('Type'), '0', array(
            'options' => array(
                '-1' => n2_('All'),
                '0'  => n2_('Public'),
                '1'  => n2_('Private')
            )
        ));


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'jomsocialorder', n2_('Order'), 'created|*|desc');
        new N2ElementList($order, 'jomsocialorder-1', n2_('Field'), '', array(
            'options' => array(
                ''             => n2_('None'),
                'name'         => n2_('Name'),
                'created'      => n2_('Creation time'),
                'discusscount' => 'Number of discussions',
                'wallcount'    => 'Number of walls',
                'membercount'  => 'Number of members',
                'hits'         => n2_('Hits'),
                'id'           => 'ID',
            )
        ));

        new N2ElementRadio($order, 'jomsocialorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    private function checkImage($url, $image, $fallback = '') {
        if (!empty($image)) {
            return N2ImageHelper::dynamic($url . '/' . $image);
        } else if (!empty($fallback)) {
            return $fallback;
        } else {
            return '';
        }
    }

    protected function _getData($count, $startIndex) {

        require_once(JPATH_SITE . '/components/com_community/router.php');

        $model = new N2Model('community_groups');

        $where = array(
            "published = 1"
        );

        $category = array_map('intval', explode('||', $this->data->get('jomsocialgroupcategories', '0')));
        if (!in_array('0', $category)) {
            $where[] = 'categoryid IN (' . implode(',', $category) . ')';
        }

        switch ($this->data->get('featured', '-1')) {
            case 1:
                $where[] = "id IN (SELECT cid FROM #__community_featured WHERE type = 'groups')";
                break;
            case -1:
                $where[] = "id NOT IN (SELECT cid FROM #__community_featured WHERE type = 'groups')";
                break;
        }

        $grouptype = $this->data->get('grouptype', '-1');
        if ($grouptype != '-1') {
            $where[] = 'approvals = ' . $grouptype;
        }

        $query = "SELECT * FROM #__community_groups WHERE " . implode(' AND ', $where) . " ";

        $order = N2Parse::parse($this->data->get('jomsocialorder', 'created|*|desc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $root = N2Uri::getBaseUri();

        $data = array();
        for ($i = 0; $i < count($result); $i++) {
            $r = array(
                'title'       => $result[$i]['name'],
                'description' => $result[$i]['description'],
                'summary'     => $result[$i]['summary']
            );

            $r['image'] = N2JoomlaImageFallBack::fallback($root . "/", array(
                @$result[$i]['avatar'],
                @$result[$i]['cover']
            ), array(
                $r['description']
            ));

            $r['thumbnail'] = $this->checkImage($root, $result[$i]['thumb'], $r['image']);

            $r += array(
                'avatar'    => $this->checkImage($root, $result[$i]['avatar']),
                'cover'     => $this->checkImage($root, $result[$i]['cover']),
                'url'       => CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $result[$i]['id']),
                'url_label' => sprintf(n2_('View %s'), n2_('group')),
                'id'        => $result[$i]['id']
            );
            $data[] = $r;
        }

        return $data;
    }
}
