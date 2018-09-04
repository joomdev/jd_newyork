<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorPhocaGalleryImages extends N2GeneratorAbstract {

    protected $layout = 'image_extended';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementPhocaGalleryCategories($source, 'phocagallerysourcecategories', n2_('Category'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementPhocaGalleryTags($source, 'phocagallerysourcetags', n2_('Tag'), 0, array(
            'isMultiple' => true
        ));


        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'), array(
            'rowClass' => 'n2-expert'
        ));
        new N2ElementText($limit, 'phocagallerysourcelanguage', n2_('Language'), '*');


        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'phocagalleryorder', n2_('Order'), 'con.date|*|desc');
        new N2ElementList($order, 'phocagalleryorder-1', n2_('Field'), '', array(
            'options' => array(
                ''             => n2_('None'),
                'con.title'    => n2_('Title'),
                'cat_title'    => n2_('Category title'),
                'con.ordering' => n2_('Ordering'),
                'con.hits'     => n2_('Hits'),
                'con.date'     => n2_('Date')
            )
        ));

        new N2ElementRadio($order, 'phocagalleryorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    protected function _getData($count, $startIndex) {

        $model = new N2Model("phocagallery");

        $categories = array_map('intval', explode('||', $this->data->get('phocagallerysourcecategories', '')));
        $tags       = array_map('intval', explode('||', $this->data->get('phocagallerysourcetags', '')));

        $query = 'SELECT ';
        $query .= 'con.id, ';
        $query .= 'con.title, ';
        $query .= 'con.alias, ';
        $query .= 'con.filename, ';
        $query .= 'con.description, ';
        $query .= 'con.hits, ';

        $query .= 'con.catid, ';
        $query .= 'cat.title AS cat_title, ';
        $query .= 'cat.description AS cat_description, ';
        $query .= 'cat.alias AS cat_alias ';

        $query .= 'FROM #__phocagallery AS con ';

        $query .= 'LEFT JOIN #__phocagallery_categories AS cat ON cat.id = con.catid ';

        $where = array(
            'con.published = 1 ',
            'con.approved = 1 '
        );
        if (count($categories) > 0 && !in_array('0', $categories)) {
            $where[] = 'con.catid IN (' . implode(',', $categories) . ') ';
        }

        if (count($tags) > 0 && !in_array('0', $tags)) {
            $where[] = 'con.id IN (SELECT imgid FROM #__phocagallery_tags_ref WHERE tagid IN (' . implode(',', $tags) . ')) ';
        }

        $language = $this->data->get('phocagallerysourcelanguage', '*');
        if ($language) {
            $where[] = 'con.language = ' . $model->db->quote($language) . ' ';
        }

        if (count($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $order = N2Parse::parse($this->data->get('phocagalleryorder', 'con.title|*|asc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $data = array();
        $uri  = N2Uri::getBaseUri();
        for ($i = 0; $i < count($result); $i++) {
            $image  = N2ImageHelper::dynamic($uri . "/images/phocagallery/" . $result[$i]['filename']);
            $r      = array(
                'image'                => $image,
                'thumbnail'            => $image,
                'title'                => $result[$i]['title'],
                'description'          => $result[$i]['description'],
                'url'                  => 'index.php?option=com_phocagallery&view=detail&catid=' . $result[$i]['catid'] . ':' . $result[$i]['cat_alias'] . '&id=' . $result[$i]['id'] . ':' . $result[$i]['alias'],
                'url_label'            => n2_('View image'),
                'filename'             => $result[$i]['filename'],
                'category_title'       => $result[$i]['cat_title'],
                'category_description' => $result[$i]['cat_description'],
                'category_url'         => 'index.php?option=com_phocagallery&view=category&id=' . $result[$i]['catid'] . ':' . $result[$i]['cat_alias'],
                'hits'                 => $result[$i]['hits'],
                'id'                   => $result[$i]['id']
            );
            $data[] = $r;
        }

        return $data;
    }
}