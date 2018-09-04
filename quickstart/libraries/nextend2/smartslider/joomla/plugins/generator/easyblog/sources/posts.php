<?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');


class N2GeneratorEasyBlogPosts extends N2GeneratorAbstract {

    protected $layout = 'article';


    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementEasyBlogCategories($source, 'easyblogcategories', n2_('Categories'), 0);
        new N2ElementEasyBlogTags($source, 'easyblogtags', n2_('Tags'), 0);

        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementText($limit, 'easybloguserid', n2_('User ID'), '');
        new N2ElementFilter($limit, 'easyblogfrontpage', n2_('Frontpage'), 0);
        new N2ElementFilter($limit, 'easyblogfeatured', n2_('Featured'), 0);
        new N2ElementText($limit, 'easyblogexclude', n2_('Exclude ID'), '');


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'easyblogorder', n2_('Order'), 'con.created|*|desc');
        new N2ElementList($order, 'easyblogorder-1', n2_('Field'), '', array(
            'options' => array(
                ''             => n2_('None'),
                'con.title'    => n2_('Title'),
                'cattitle'     => n2_('Category title'),
                'blogger'      => n2_('Username'),
                'con.ordering' => n2_('Ordering'),
                'con.created'  => n2_('Creation time'),
                'con.modified' => n2_('Modification time')
            )
        ));

        new N2ElementRadio($order, 'easyblogorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    private function findImage($path, $url) {
        $locations = array(
            'easyblog_images',
            'easyblog_articles',
            'easyblog_shared',
            'easyblog_cavatar',
            'easyblog_tavatar'
        );

        $pathlocation = '';

        foreach ($locations AS $l) {
            if (strpos($path, $l)) {
                $pathlocation = $l;
                break;
            }
        }

        if ($pathlocation != '') {
            foreach ($locations AS $l) {
                if ($pathlocation != $l) {
                    if (file_exists(str_replace($pathlocation, $l, $path))) {
                        return str_replace($pathlocation, $l, $url);
                        break;
                    }
                }
            }
        }
    }

    protected function _getData($count, $startIndex) {
        require_once(JPATH_ADMINISTRATOR . "/components/com_easyblog/includes/easyblog.php");
        EB::mediamanager();

        $model = new N2Model('EasyBlog_Post');

        $category = array_map('intval', explode('||', $this->data->get('easyblogcategories', '')));

        $query = 'SELECT con.*, con.intro as "main_content_of_post", con.content as "rest_of_the_post", usr.nickname as "blogger", usr.avatar as "blogger_avatar_picture", cat.title as cat_title ';

        /* id 	created_by 	title 	description 	alias 	avatar 	parent_id 	private 	created 	status 	published 	ordering 	level 	lft 	rgt 	default */

        $query .= 'FROM #__easyblog_post con ';

        $query .= 'LEFT JOIN #__easyblog_users usr ON usr.id = con.created_by ';

        $query .= 'LEFT JOIN #__easyblog_category cat ON cat.id = con.category_id ';

        $jnow  = JFactory::getDate();
        $now   = $jnow->toSql();
        $where = array("con.published = 1 AND (con.publish_up = '0000-00-00 00:00:00' OR con.publish_up < '" . $now . "') AND (con.publish_down = '0000-00-00 00:00:00' OR con.publish_down > '" . $now . "') ");

        $exclude = $this->data->get('easyblogexclude', '');
        if (!empty($exclude)) {
            $where[] = ' con.id NOT IN (' . $exclude . ') ';
        }

        if (!in_array('0', $category)) {
            $where[] = 'con.category_id IN (' . implode(',', $category) . ') ';
        }

        $tags = array_map('intval', explode('||', $this->data->get('easyblogtags', '0')));

        if (!in_array(0, $tags)) {
            $where[] = 'con.id IN (SELECT post_id FROM #__easyblog_post_tag WHERE tag_id IN(' . implode(',', $tags) . '))';
        }

        switch ($this->data->get('easyblogfrontpage', 0)) {
            case 1:
                $where[] = "con.frontpage = 1 ";
                break;
            case -1:
                $where[] = "con.frontpage = 0 ";
                break;
        }

        switch ($this->data->get('easyblogfeatured', 0)) {
            case 1:
                $where[] = "con.id IN (SELECT content_id FROM #__easyblog_featured WHERE type = 'post')";
                break;
            case -1:
                $where[] = "con.id NOT IN (SELECT content_id FROM #__easyblog_featured WHERE type = 'post')";
                break;
        }

        $sourceUserId = intval($this->data->get('easybloguserid', ''));
        if (!empty($sourceUserId)) {
            $where[] = 'con.created_by = ' . $sourceUserId . ' ';
        }

        $where[] = " con.state = 0 ";

        if (count($where) > 0) {
            $query .= 'WHERE ' . implode(' AND ', $where) . ' ';
        }

        $order = N2Parse::parse($this->data->get('easyblogorder', 'con.title|*|asc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count . ' ';

        $result = $model->db->queryAll($query);

        $data = array();
        $root = N2Uri::getBaseUri();
        for ($i = 0; $i < count($result); $i++) {
            $description = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $result[$i]['main_content_of_post']);

            $url = 'index.php?option=com_easyblog&view=entry&id=' . $result[$i]['id'];
            if (class_exists('EBR', false)) {
                $url = EBR::_($url, true, null, false, false, false);
            }

            $r = array(
                'title'       => $result[$i]['title'],
                'description' => $description,
                'url'         => $url,
            );

            if (!empty($result[$i]['image'])) {
                $imageUrl = EBMM::getUrl($result[$i]['image']);
                $filename = EBMM::getTitle($result[$i]['image']);
                $filepath = EBMM::getPath($result[$i]['image']);
                if (file_exists($filepath)) {
                    $fullRoot = str_replace($filename, '', $imageUrl);
                    $image    = $filename;
                } else {
                    $newImageUrl = $this->findImage($filepath, $imageUrl);
                    if (!empty($newImageUrl)) {
                        $fullRoot = str_replace($filename, '', $newImageUrl);
                        $image    = $filename;
                    } else {
                        $fullRoot = $root;
                        $image    = '';
                    }
                }
            } else {
                $fullRoot = $root;
                $image    = '';
            }

            $r['image'] = $r['thumbnail'] = N2JoomlaImageFallBack::fallback($fullRoot, array($image), array($result[$i]['content']));
            $content    = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $result[$i]['content']);

            $category_url = 'index.php?option=com_easyblog&view=categories&id=' . $result[$i]['category_id'];
            if (class_exists('EBR', false)) {
                $category_url = EBR::_($category_url);
            }

            $r += array(
                'url_label'              => sprintf(n2_('View %s'), n2_('post')),
                'category_url'           => $category_url,
                'category_title'         => $result[$i]['cat_title'],
                'blogger'                => $result[$i]['blogger'],
                'blogger_avatar_picture' => ($result[$i]['blogger_avatar_picture'] == "default_blogger.png" ? "components/com_easyblog/assets/images/" . $result[$i]['blogger_avatar_picture'] : "images/easyblog/avatar/" . $result[$i]['blogger_avatar_picture']),
                'created_by_id'          => $result[$i]['created_by'],
                'creation_time'          => $result[$i]['created'],
                'modification_time'      => $result[$i]['modified'],
                'content'                => $content,
                'latitude'               => $result[$i]['latitude'],
                'longitude'              => $result[$i]['longitude'],
                'address'                => $result[$i]['address'],
                'hits'                   => $result[$i]['hits'],
                'category_id'            => $result[$i]['category_id'],
                'id'                     => $result[$i]['id'],
            );
            $data[] = $r;
        }

        return $data;
    }
}
