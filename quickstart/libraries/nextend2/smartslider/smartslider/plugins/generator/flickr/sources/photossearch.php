<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorFlickrPhotosSearch extends N2GeneratorAbstract {

    protected $layout = 'image';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        new N2ElementText($filter, 'userID', n2_('User name or ID'), 'me');
        new N2ElementText($filter, 'tags', n2_('Tags'), '');
        new N2ElementText($filter, 'text', n2_('Search in title, description or tags'), '');

        new N2ElementList($filter, 'privacy', n2_('Privacy'), 1, array(
            'options' => array(
                '1' => 'Public photos',
                '2' => 'Private photos visible to friends',
                '3' => 'Private photos visible to family',
                '4' => 'Private photos visible to friends &amp; family',
                '5' => 'Completely private photos'
            )
        ));
        new N2ElementList($filter, 'type', n2_('Type'), 7, array(
            'options' => array(
                '7' => n2_('All'),
                '1' => 'Photos only',
                '2' => 'Screenshots only',
                '3' => '\'Other\' only',
                '4' => 'Photos and screenshots',
                '5' => 'Screenshots and \'other\'',
                '6' => 'Photos and \'other\''

            )
        ));

    }

    protected function _getData($count, $startIndex) {
        $client = $this->group->getConfiguration()
                              ->getApi();

        $userID  = $this->data->get('userID', 'me');
        $tags    = $this->data->get('tags', '');
        $text    = $this->data->get('text', '');
        $privacy = $this->data->get('privacy', '1');
        $type    = $this->data->get('type', '1');

        $args   = array(
            'tags'           => $tags,
            'user_id'        => $userID,
            'text'           => $text,
            'privacy_filter' => $privacy,
            'content_type'   => $type,
            'per_page'       => $count
        );
        
        $result = $client->photos_search($args);
        if (is_array($result['photos']) && !empty($result['photos'])) {
            $photos = $result['photos']['photo'];
        } else {
            return null;
        }

        $data = array();
        foreach ($photos AS $photo) {
            if (!isset($ow)) {
                $ow = $client->people_getInfo($photo['owner']);
            }
            $image  = 'https://c2.staticflickr.com/' . $photo['farm'] . '/' . $photo['server'] . '/' . $photo['id'] . '_' . $photo['secret'];
            $r      = array(
                'image'     => $image . '_b.jpg',
                'thumbnail' => $image . '_m.jpg',
                'title'     => $photo['title'],
                'url'       => 'https://www.flickr.com/photos/' . $photo['owner'] . '/' . $photo['id'],
                'url_b'     => $image . '_b.jpg',
                'url_c'     => $image . '_c.jpg',
                'url_h'     => $image . '_h.jpg',
                'url_m'     => $image . '_m.jpg',
                'url_n'     => $image . '_n.jpg',
                'url_s'     => $image . '_s.jpg',
                'url_t'     => $image . '_t.jpg',
                'url_q'     => $image . '_q.jpg',
                'url_z'     => $image . '_z.jpg'
            );
            $data[] = $r;
        }

        return $data;
    }
}