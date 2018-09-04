<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorFacebookAlbums extends N2GeneratorAbstract {

    protected $layout = 'image_extended';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'album', n2_('Album'));

        new N2ElementText($filter, 'facebook-id', n2_('User or page'), 'me');
        new N2ElementFacebookAlbums($filter, 'facebook-album-id', n2_('Album'), '', array(
            'api' => $this->group->getConfiguration()
                                 ->getApi()
        ));
    }

    protected function _getData($count, $startIndex) {

        $api = $this->group->getConfiguration()
                           ->getApi();

        $albumId = $this->data->get('facebook-album-id', '');

        $data = array();
        try {
            $result = $api->sendRequest('GET', $albumId . '/photos', array(
                'offset' => $startIndex,
                'limit'  => $count,
                'fields' => implode(',', array(
                    'from',
                    'images',
                    'name',
                    'link',
                    'likes',
                    'comments',
                    'icon',
                    'picture',
                    'source'
                ))
            ))
                          ->getDecodedBody();
            for ($i = 0; $i < count($result['data']); $i++) {
                $post = $result['data'][$i];

                $record                = array();
                $record['image']       = $post['images'][0]['source'];
                $record['thumbnail']   = $post['images'][count($post['images']) - 1]['source'];
                $record['title']       = $post['from']['name'];
                $record['description'] = isset($post['name']) ? $this->makeClickableLinks($post['name']) : '';

                $record['url']       = $record['link'] = $post['link'];
                $record['url_label'] = 'View image';

                $record['author_url'] = 'https://www.facebook.com/' . $post['from']['id'];

                $record['likes']    = isset($post['likes']) && isset($post['likes']['data']) ? count($post['likes']['data']) : 0;
                $record['comments'] = isset($post['comments']) && isset($post['comments']['data']) ? count($post['comments']['data']) : 0;

                $record['icon']    = $post['icon'];
                $record['picture'] = $post['picture'];
                $record['source']  = $post['source'];

                $x = 1;
                foreach ($post['images'] AS $img) {
                    if ($x == 2 && $img["height"] < 960 && $img["width"] < 960) {
                        $record['image' . $x] = $img['source'];
                        $x++;
                    }
                    $record['image' . $x] = $img['source'];
                    $x++;
                }

                if ($x < 10) {
                    while ($x < 10) {
                        $record['image' . $x] = $img['source'];
                        $x++;
                    }
                }

                $data[$i] = &$record;
                unset($record);
            }
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }

        return $data;
    }
}