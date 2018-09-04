<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorFlickrPeoplePhotoStream extends N2GeneratorAbstract {

    protected $layout = 'image_extended';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        new N2ElementList($filter, 'peoplephotostreamprivacy', n2_('Privacy'), 1, array(
            'options' => array(
                '1' => 'Public photos',
                '2' => 'Private photos visible to friends',
                '3' => 'Private photos visible to family',
                '4' => 'Private photos visible to friends &amp; family',
                '5' => 'Completely private photos'
            )
        ));
    }

    private function exists($photo, $empty = '') {
        if (isset($photo)) {
            return $photo;
        } else {
            return $empty;
        }
    }

    protected function _getData($count, $startIndex) {
        $data = array();

        $client = $this->group->getConfiguration()
                              ->getApi();

        $peoplephotostreamprivacy = intval($this->data->get('peoplephotostreamprivacy', 1));

        $result = $client->people_getPhotos('me', array(
            'per_page'       => $startIndex + $count,
            'privacy_filter' => $peoplephotostreamprivacy,
            'extras'         => 'description, date_upload, date_taken, owner_name, geo, tags, o_dims, views, media, path_alias, url_sq, url_t, url_s, url_q, url_m, url_n, url_z, url_c, url_l, url_o'
        ));

        if (is_array($result['photos']['photo']) && !empty($result['photos']['photo'])) {
            $photos = array_slice($result['photos']['photo'], $startIndex, $count);
        } else {
            N2Message::error(n2_('There are no photos with this privacy filter!'));

            return null;
        }

        $ownerCache = array();

        $i = 0;
        foreach ($photos AS $photo) {
            if (!isset($ownerCache[$photo['ownername']])) {
                $owner                           = $client->people_findByUsername($photo['ownername']);
                $ownerCache[$photo['ownername']] = $client->people_getInfo($owner['user']['nsid']);
            }
            $ow = $ownerCache[$photo['ownername']];

            $data[$i]['image']       = $this->exists($photo['url_o'], $photo['url_l']);
            $data[$i]['thumbnail']   = $this->exists($photo['url_m'], $photo['url_l']);
            $data[$i]['title']       = $photo['title'];
            $data[$i]['description'] = $photo['description']['_content'];
            $data[$i]['url']         = $ow['person']['photosurl']['_content'];
            $data[$i]['url_label']   = n2_('View');

            $data[$i]['owner_username']       = $ow['person']['username']['_content'];
            $data[$i]['author_name']          = $this->exists($ow['person']['realname']['_content'], $ow['person']['username']['_content']);
            $data[$i]['author_url']           = $ow['person']['profileurl']['_content'];
            $data[$i]['url_t']                = $this->exists(@$photo['url_t']);
            $data[$i]['url_s']                = $this->exists(@$photo['url_s']);
            $data[$i]['url_q']                = $this->exists(@$photo['url_q']);
            $data[$i]['url_m']                = $this->exists(@$photo['url_m']);
            $data[$i]['url_n']                = $this->exists(@$photo['url_n']);
            $data[$i]['url_z']                = $this->exists(@$photo['url_z']);
            $data[$i]['url_c']                = $this->exists(@$photo['url_c']);
            $data[$i]['url_l']                = $this->exists(@$photo['url_l']);
            $data[$i]['url_o']                = $this->exists(@$photo['url_o']);
            $data[$i]['owner']                = $photo['owner'];
            $data[$i]['dateupload']           = $photo['dateupload'];
            $data[$i]['datetaken']            = $photo['datetaken'];
            $data[$i]['datetakengranularity'] = $photo['datetakengranularity'];
            $data[$i]['datetakenunknown']     = $photo['datetakenunknown'];
            $data[$i]['ownername']            = $photo['ownername'];
            $data[$i]['views']                = $photo['views'];
            $data[$i]['tags']                 = $photo['tags'];
            $data[$i]['latitude']             = $photo['latitude'];
            $data[$i]['longitude']            = $photo['longitude'];
            $data[$i]['accuracy']             = $photo['accuracy'];
            $data[$i]['context']              = $photo['context'];
            $data[$i]['media']                = $photo['media'];
            $data[$i]['media_status']         = $photo['media_status'];
            $data[$i]['url_sq']               = $photo['url_sq'];
            $i++;
        }

        return $data;
    }

}
