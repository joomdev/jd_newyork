<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorVimeoAlbum extends N2GeneratorAbstract {

    protected $layout = 'vimeo';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        new N2ElementVimeoAlbums($filter, 'album', 'Album', '', array(
            'api' => $this->group->getConfiguration()
                ->getApi()
        ));

        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'vimeoorder', n2_('Order'), '');
        new N2ElementList($order, 'vimeoorder-1', n2_('Sort'), '', array(
            'options' => array(
                ''              => n2_('None'),
                'alphabetical'  => n2_('Alphabetical'),
                'comments'      => n2_('Comments'),
                'date'          => n2_('Date'),
                'default'       => n2_('Default'),
                'duration'      => n2_('Duration'),
                'likes'         => n2_('Likes'),
                'manual'        => n2_('Manual'),
                'modified_time' => n2_('Modified Time'),
                'plays'         => n2_('Plays')
            )
        ));
    }

    protected function _getData($count, $startIndex) {
        $data = array();
        /** @var \Vimeo\Vimeo $api */
        $api = $this->group->getConfiguration()
            ->getApi();

        $album = $this->data->get('album', '');
        if (!empty($album)) {
            $args = array(
                'per_page' => $startIndex + $count
            );

            $order = N2Parse::parse($this->data->get('vimeoorder', ''));
            if (!empty($order)) {
                $args['sort'] = $order;
            }

            $response = $api->request($album . '/videos', $args);

            if ($response['status'] == 200) {
                $videos = array_slice($response['body']['data'], $startIndex, $count);

                foreach ($videos AS $video) {
                    $record = array();

                    $record['title']       = $video['name'];
                    $record['description'] = $video['description'];
                    $record['id']          = str_replace('/videos/', '', $video['uri']);
                    $record['url']         = 'https://vimeo.com/' . $record['id'];
                    $record['link']        = $video['link'];

                    foreach ($video['pictures']['sizes'] AS $picture) {
                        $record['image' . $picture['width'] . 'x' . $picture['height']]     = $picture['link'];
                        $record['imageplay' . $picture['width'] . 'x' . $picture['height']] = $picture['link_with_play_button'];
                    }

                    $data[] = &$record;
                    unset($record);
                }
            }
        }

        return $data;
    }
}