<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorIgniteGalleryImages extends N2GeneratorAbstract {

    protected $layout = 'image_extended';


    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementIgniteGalleryCategories($source, 'ignitegallerysourcecategory', n2_('Category'), 0, array(
            'isMultiple' => true
        ));

        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'ignitegalleryorder', n2_('Order'), 'con.date|*|desc');
        new N2ElementList($order, 'ignitegalleryorder-1', n2_('Field'), '', array(
            'options' => array(
                ''             => n2_('None'),
                'con.filename' => n2_('Filename'),
                'cat_title'    => n2_('Category'),
                'con.ordering' => n2_('Ordering'),
                'con.hits'     => n2_('Hits'),
                'con.date'     => n2_('Creation time')
            )
        ));

        new N2ElementRadio($order, 'ignitegalleryorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    protected function _getData($count, $startIndex) {
        require_once(JPATH_ADMINISTRATOR . '/components/com_igallery/defines.php');

        $categories = array_map('intval', explode('||', $this->data->get('ignitegallerysourcecategory', '')));

        $model = new N2Model('igallery_img');

        $query = 'SELECT ';
        $query .= 'con.id, ';
        $query .= 'con.filename, ';
        $query .= 'con.description, ';
        $query .= 'con.alt_text, ';
        $query .= 'con.link, ';
        $query .= 'con.hits, ';
        $query .= 'con.rotation, ';

        $query .= 'con.gallery_id, ';
        $query .= 'cat.name AS cat_title, ';
        $query .= 'cat.alias AS cat_alias ';

        $query .= 'FROM #__igallery_img AS con ';

        $query .= 'LEFT JOIN #__igallery AS cat ON cat.id = con.gallery_id ';

        $where = array('con.published = 1 ');
        if (count($categories) > 0 && !in_array('0', $categories)) {
            $where[] = 'con.gallery_id IN (' . implode(',', $categories) . ') ';
        }

        if (count($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }

        $order = N2Parse::parse($this->data->get('ignitegalleryorder', 'con.ordering|*|asc'));
        if ($order[0]) {
            $query .= 'ORDER BY ' . $order[0] . ' ' . $order[1] . ' ';
        }

        $query .= 'LIMIT ' . $startIndex . ', ' . $count . ' ';

        $result = $model->db->queryAll($query);

        $root = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
        $root .= '://' . $_SERVER['SERVER_NAME'];

        $data = array();

        for ($i = 0; $i < count($result); $i++) {
            $fileHashNoExt = JFile::stripExt($result[$i]['filename']);
            $fileHashNoRef = substr($fileHashNoExt, 0, strrpos($fileHashNoExt, '-'));

            $increment  = igFileHelper::getIncrementFromFilename($result[$i]['filename']);
            $folderName = igFileHelper::getFolderName($increment);
            $sourceFile = IG_ORIG_PATH . '/' . $folderName . '/' . $result[$i]['filename'];
            $size       = getimagesize($sourceFile);

            $fileArray = igFileHelper::originalToResized($result[$i]['filename'], $size[0], $size[1], 100, 0, $result[$i]['rotation'], 0, 0);

            $result[$i]['thumbnail'] = $result[$i]['image'] = N2ImageHelper::dynamic($root . IG_IMAGE_HTML_RESIZE . $fileArray['folderName'] . '/' . $fileArray['fullFileName']);

            $result[$i]['url']          = $result[$i]['image_url'] = 'index.php?option=com_igallery&view=category&igid=' . $result[$i]['gallery_id'] . '#!' . $fileHashNoRef;
            $result[$i]['category_url'] = 'index.php?option=com_igallery&view=category&igid=' . $result[$i]['gallery_id'];
            if (!empty($result[$i]['link'])) {
                $result[$i]['url'] = $result[$i]['link'];
            }
            $result[$i]['url_label'] = n2_('View');
            if (!empty($result[$i]['alt_text'])) {
                $result[$i]['title'] = $result[$i]['alt_text'];
            } else {
                $result[$i]['title'] = $result[$i]['filename'];
            }

            $r = array(
                'image'          => $result[$i]['image'],
                'thumbnail'      => $result[$i]['thumbnail'],
                'title'          => $result[$i]['title'],
                'description'    => $result[$i]['description'],
                'url'            => $result[$i]['url'],
                'url_label'      => $result[$i]['url_label'],
                'filename'       => $result[$i]['filename'],
                'image_url'      => $result[$i]['image_url'],
                'hits'           => $result[$i]['hits'],
                'category_title' => $result[$i]['cat_title'],
                'category_url'   => $result[$i]['category_url'],
                'id'             => $result[$i]['id']
            );

            $data[] = $r;
        }

        return $data;
    }

}
