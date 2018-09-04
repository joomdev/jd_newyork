<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');
require_once(JPATH_SITE . '/components/com_content/helpers/route.php');

class N2GeneratorJReviewsComments extends N2GeneratorAbstract {

    protected $layout = 'article';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementJReviewsCategories($source, 'sourcecategories', n2_('Category'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementJReviewsArticles($source, 'sourcearticles', n2_('Article'), 0, array(
            'isMultiple' => true
        ));


        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementList($limit, 'sourcestars', 'Minimum star rating', 1, array(
            'options' => array(
                ''  => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5'
            )
        ));
        new N2ElementText($limit, 'sourcehelpful', 'Minimum thumbs up number', 0);


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'jreviewsorder', n2_('Order'), 'jc.created|*|desc');
        new N2ElementList($order, 'jreviewsorder-1', n2_('Field'), '', array(
            'options' => array(
                ''                => n2_('None'),
                'jc.created'      => n2_('Creation time'),
                'jc.modified'     => n2_('Modification time'),
                'jc.rating'       => 'Rating',
                'jc.vote_helpful' => 'Thumbs up',
                'jc.vote_total'   => 'Total number of votes',
                'c.hits'          => n2_('Hits')
            )
        ));

        new N2ElementRadio($order, 'jreviewsorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    function imageUrl($image) {
        $root = JURI::root();
        if (!empty($image)) {
            return N2ImageHelper::dynamic($root . $image);
        } else {
            return '';
        }
    }

    protected function _getData($count, $startIndex) {
        $model = new N2Model('jreviews_categories');

        $categories = array_map('intval', explode('||', $this->data->get('sourcecategories', '')));
        $articles   = array_map('intval', explode('||', $this->data->get('sourcearticles', '')));

        $where = array(
            'jc.published = 1',
            'jc.mode = \'com_content\''
        );

        $articleWhere = '';
        if (!in_array(0, $articles) && count($articles) > 0) {
            $articleWhere = ' AND asset_id IN (' . implode(',', $articles) . ') ';
        }

        if (!in_array(0, $categories) && count($categories) > 0) {
            $where[] = 'jc.pid IN (SELECT id FROM #__content WHERE asset_id IN (SELECT id FROM #__assets WHERE parent_id IN (' . implode(',', $categories) . '))' . $articleWhere . ')';
        } else if (!in_array(0, $articles) && count($articles) > 0) {
            $where[] = 'jc.pid IN (SELECT id FROM #__content WHERE asset_id IN (' . implode(',', $articles) . '))';
        }

        $stars = $this->data->get('sourcestars', '');
        if (!empty($stars)) {
            $where[] = 'jc.rating >= ' . $stars;
        }

        $helpful = intval($this->data->get('sourcehelpful', ''));
        if (!empty($helpful) && $helpful > 0) {
            $where[] = 'jc.vote_helpful >= ' . $helpful;
        }

        $query = 'SELECT *,jc.title AS comment_title FROM #__jreviews_comments AS jc
                  LEFT JOIN #__content AS c ON jc.pid = c.id
                  WHERE ' . implode(' AND ', $where);

        $order = N2Parse::parse($this->data->get('jreviewsorder', 'jc.created|*|desc'));
        if ($order[0]) {
            $query .= ' ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= ' LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $data = array();

        foreach ($result AS $res) {
            $r = array(
                'title'       => $res['comment_title'],
                'description' => $res['comments']
            );

            $query = 'SELECT media_type, filename, file_extension, rel_path, title, description, embed FROM #__jreviews_media
                      WHERE listing_id = ' . $res['pid'] . ' AND published = 1 AND approved = 1';
            $model = new N2Model('jreviews_media');
            $media = $model->db->queryAll($query);

            $i = 1;
            foreach ($media AS $m) {
                if ($m['media_type'] == 'photo') {
                    $r['photo' . $i] = $this->imageUrl('media/reviews/photos/' . $m['rel_path'] . $m['filename'] . '.' . $m['file_extension']);
                    $i++;
                }
            }

            $article_images = json_decode($res['images']);

            if (isset($r['photo1'])) {
                $r['image'] = $r['photo1'];
            } else if (!empty($article_images->image_intro)) {
                $r['image'] = $this->imageUrl($article_images->image_intro);
            } else if (isset($article_images->image_fulltext)) {
                $r['image'] = $this->imageUrl($article_images->image_fulltext);
            } else {
                $r['image'] = '';
            }

            $r['thumbnail'] = $r['image'];

            $r += array(
                'title'             => $res['comment_title'],
                'description'       => $res['comments'],
                'url'               => ContentHelperRoute::getArticleRoute($res['id'], $res['catid']),
                'url_label'         => sprintf(n2_('View %s'), n2_('article')),
                'rating'            => round($res['rating'], 1),
                'name'              => $res['name'],
                'username'          => $res['username'],
                'email'             => $res['email'],
                'location'          => $res['location'],
                'creation_time'     => $res['created'],
                'vote_helpful'      => $res['vote_helpful'],
                'vote_total'        => $res['vote_total'],
                'review_note'       => $res['review_note'],
                'article_title'     => $res['title'],
                'article_introtext' => $res['introtext'],
                'article_fulltext'  => $res['fulltext'],
                'article_hits'      => $res['hits']
            );

            if (isset($article_images->image_intro)) $r['article_introtext_image'] = $this->imageUrl($article_images->image_intro);
            if (isset($article_images->image_fulltext)) $r['article_fulltext_image'] = $this->imageUrl($article_images->image_fulltext);

            $data[] = $r;
        }

        return $data;
    }

}
