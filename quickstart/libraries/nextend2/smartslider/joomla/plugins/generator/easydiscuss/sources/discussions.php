<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');


class N2GeneratorEasyDiscussDiscussions extends N2GeneratorAbstract {

    protected $layout = 'article';


    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementEasyDiscussCategories($source, 'easydiscusscategories', n2_('Category'), 0);
        new N2ElementEasyDiscussTags($source, 'easydiscusstags', n2_('Tags'), 0);

        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementText($limit, 'easydiscussuserid', n2_('User ID'), '');
        new N2ElementFilter($limit, 'easydiscussfeatured', n2_('Featured'), 0);
        new N2ElementFilter($limit, 'easydiscussresolved', n2_('Resolved'), 0);
        new N2ElementOnOff($limit, 'easydiscussmain', n2_('Only main discussions'), 1);


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'easydiscussorder', n2_('Order'), 'created|*|desc');
        new N2ElementList($order, 'easydiscussorder-1', n2_('Field'), '', array(
            'options' => array(
                ''         => n2_('None'),
                'title'    => n2_('Title'),
                'cattitle' => n2_('Category title'),
                'ordering' => n2_('Ordering'),
                'created'  => n2_('Creation time'),
                'modified' => n2_('Modification time')
            )
        ));

        new N2ElementRadio($order, 'easydiscussorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    protected function _getData($count, $startIndex) {

        $model = new N2Model('EasyDiscuss_Discussions');

        $category = array_map('intval', explode('||', $this->data->get('easydiscusscategories', '')));

        $where = array("published = '1'");

        if (!in_array('0', $category)) {
            $where[] = 'category_id IN (' . implode(',', $category) . ') ';
        }

        $tags = array_map('intval', explode('||', $this->data->get('easydiscusstags', '0')));

        if (!in_array(0, $tags)) {
            $where[] = 'id IN (SELECT post_id FROM #__discuss_posts_tags WHERE tag_id IN(' . implode(',', $tags) . ')) ';
        }

        switch ($this->data->get('easydiscussfeatured', 0)) {
            case 1:
                $where[] = "featured = 1 ";
                break;
            case -1:
                $where[] = "featured = 0 ";
                break;
        }

        switch ($this->data->get('easydiscussresolved', 0)) {
            case 1:
                $where[] = "isresolve = 1 ";
                break;
            case -1:
                $where[] = "isresolve = 0 ";
                break;
        }

        $sourceUserId = intval($this->data->get('easydiscussuserid', ''));
        if (!empty($sourceUserId)) {
            $where[] = 'user_id = ' . $sourceUserId . ' ';
        }

        $sourceDiscussionMain = intval($this->data->get('easydiscussmain', ''));
        if (!empty($sourceDiscussionMain)) {
            $where[] = "parent_id = '0' ";
        }

        $query = 'SELECT * FROM #__discuss_posts WHERE ' . implode(' AND ', $where) . ' ';

        $order = N2Parse::parse($this->data->get('easydiscussorder', 'created|*|desc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count . ' ';

        $result = $model->db->queryAll($query);

        $data = array();
        for ($i = 0; $i < count($result); $i++) {
            $user = JFactory::getUser($result[$i]['user_id']);
            $r    = array(
                'title'           => $result[$i]['title'],
                'description'     => $result[$i]['content'],
                'url'             => 'index.php?option=com_easydiscuss&view=post&id=' . $result[$i]['id'],
                'url_label'       => sprintf(n2_('View %s'), n2_('discussion')),
                'category_url'    => 'index.php?option=com_easydiscuss&view=categories&layout=listings&category_id=' . $result[$i]['category_id'],
                'user_name'       => $user->username,
                'user_real_name'  => $user->name,
                'vote'            => $result[$i]['vote'],
                'hits'            => $result[$i]['hits'],
                'number_of_likes' => $result[$i]['num_likes'],
                'number_of_votes' => $result[$i]['sum_totalvote'],
                'created'         => $result[$i]['created'],
                'modified'        => $result[$i]['modified'],
                'user_id'         => $result[$i]['user_id'],
                'latitude'        => $result[$i]['latitude'],
                'longitude'       => $result[$i]['longitude'],
                'parent_id'       => $result[$i]['parent_id'],
                'category_id'     => $result[$i]['category_id'],
                'id'              => $result[$i]['id']
            );

            $r['image'] = $r['thumbnail'] = N2JoomlaImageFallBack::fallback(N2Uri::getBaseUri(), array(), array($result[$i]['content']));

            $data[] = $r;
        }

        return $data;
    }
}
