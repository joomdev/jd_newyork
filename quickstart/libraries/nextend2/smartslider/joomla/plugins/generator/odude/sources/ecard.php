<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorODudeECard extends N2GeneratorAbstract {

    protected $layout = 'image_extended';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementODudeCategories($source, 'odudecategories', n2_('Category'), 0, array(
            'isMultiple' => true
        ));
        new N2ElementList($source, 'odudetypes', 'Type', 'J', array(
            'options' => array(
                'J' => 'JPEG or GIF E-Card',
                'F' => 'FLASH / SWF E-Card'
            )
        ));

        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'odudeorder', n2_('Order'), 'ddate|*|desc');
        new N2ElementList($order, 'odudeorder-1', n2_('Field'), '', array(
            'options' => array(
                ''         => n2_('None'),
                'ordering' => n2_('Ordering'),
                'ddate'    => n2_('Date'),
                'hits'     => n2_('Hits'),
                'title'    => n2_('Title'),
                'id'       => 'ID'
            )
        ));

        new N2ElementRadio($order, 'odudeorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    protected function _getData($count, $startIndex) {
        $model = new N2Model("ecard_media");

        $categories = array_map('intval', explode('||', $this->data->get('odudecategories', '')));

        $where = array(
            "type = '" . $this->data->get('odudetypes', 'J') . "'",
            'published = 1'
        );

        if (!in_array(0, $categories)) {
            $where[] = 'cat IN (' . implode(',', $categories) . ')';
        }

        $order = N2Parse::parse($this->data->get('odudeorder', 'ddate|*|desc'));
        if ($order[0]) {
            $orderBy = 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query = 'SELECT * FROM #__ecard_media WHERE ' . implode(' AND ', $where) . ' ' . $orderBy . ' LIMIT ' . $startIndex . ', ' . $count;

        $result = $model->db->queryAll($query);

        $root = JURI::root();
        $data = array();
        foreach ($result AS $card) {
            $r      = array(
                'image'            => N2ImageHelper::dynamic($root . 'media/ecard/' . $card['file']),
                'thumbnail'        => N2ImageHelper::dynamic($root . 'media/ecard/' . $card['thumb']),
                'title'            => $card['title'],
                'description'      => $card['code'],
                'meta_description' => $card['desp'],
                'url'              => JRoute::_('index.php?option=com_odudecard&id=' . $card['id'] . '&controller=odudecardshow&cate=' . $card['cat']),
                'url_label'        => sprintf(n2_('View %s'), n2_('E-card')),
                'category_url'     => JRoute::_('index.php?option=com_odudecard&controller=odudecardlist&cate=' . $card['cat']),
                'hits'             => $card['hits'],
                'file'             => $card['file'],
                'point'            => $card['point'],
                'created_by'       => $card['username'],
                'creation_date'    => $card['ddate']
            );
            $data[] = $r;
        }

        return $data;
    }
}