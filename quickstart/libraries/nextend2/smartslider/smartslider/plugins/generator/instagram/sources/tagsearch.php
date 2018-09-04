<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorInstagramTagSearch extends N2GeneratorAbstract {

    private $resultPerPage = 20;
    private $pages = array();
    /** @var  Instagram */
    private $client;

    protected $layout = 'image_extended';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        new N2ElementText($filter, 'keyword', n2_('Search by tag'), '');
    }

    protected function _getData($count, $startIndex) {
        $this->client = $this->group->getConfiguration()
                                    ->getApi();

        $data = array();
        try {

            $offset = $startIndex;
            $limit  = $count;
            $shift  = 0;
            for ($i = 0, $j = $offset; $j - $shift < $offset + $limit; $i++, $j++) {

                $items = $this->getPage(intval(($j + $shift) / $this->resultPerPage));

                if (empty($items[($j + $shift) % $this->resultPerPage])) {
                    // There is no more item in the list
                    break;
                }

                $item = $items[($j + $shift) % $this->resultPerPage];

                if ($item['type'] == 'image' || $item['type'] == 'carousel') {
                    $record                = array();
                    $record['title']       = $record['caption'] = is_array($item['caption']) ? $item['caption']['text'] : '';
                    $record['image']       = $record['standard_res_image'] = $item['images']['standard_resolution']['url'];
                    $record['thumbnail']   = $record['thumbnail_image'] = $item['images']['thumbnail']['url'];
                    $record['description'] = n2_('Description is not available');
                    $record['url']         = $item['link'];
                    $record['url_label']   = n2_('View image');
                    $record['author_name'] = $record['owner_full_name'] = $item['user']['full_name'];
                    $record['author_url']  = $record['owner_website'] = (isset($item['user']['website']) ? $item['user']['website'] : '#');

                    $record['low_res_image']         = $item['images']['low_resolution']['url'];
                    $record['owner_username']        = $item['user']['username'];
                    $record['owner_profile_picture'] = $item['user']['profile_picture'];
                    $record['owner_bio']             = isset($item['user']['bio']) ? $item['user']['bio'] : '';
                    $record['likes_count']           = $item['likes']['count'];

                    $record['comments_count'] = $item['comments']['count'];

                    if ($record['comments_count'] > 0) {
                        foreach ($item['comments']['data'] AS $x => $comment) {
                            $x++;
                            $record['comments' . $x]                      = $comment['text'];
                            $record['comments' . $x . '_username']        = $comment['from']['username'];
                            $record['comments' . $x . '_profile_picture'] = $comment['from']['profile_picture'];
                        }
                    }

                    $data[$i] = &$record;
                    unset($record);
                } else {
                    $shift++;
                }
            }
            if (is_array($data)) {
                $data = array_values($data);
            }
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }

        return $data;
    }

    private function getPage($page) {
        if (!isset($this->pages[$page])) {
            $max_id = null;
            if ($page != 0) {
                $previousPage = $this->getPage($page - 1);
                $max_id       = $previousPage[count($previousPage) - 1]['id'];
            }
            $response = json_decode($this->client->getRecentTags($this->data->get('keyword', ''), $max_id, null, $this->resultPerPage), true);
            if ($response['meta']['code'] == 200) {
                $this->pages[$page] = $response['data'];
            }
        }

        return $this->pages[$page];
    }
}