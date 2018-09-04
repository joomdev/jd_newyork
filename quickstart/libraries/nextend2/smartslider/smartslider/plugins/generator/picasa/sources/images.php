<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorPicasaImages extends N2GeneratorAbstract {

    protected $layout = 'images';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        new N2ElementPicasaAlbums($filter, 'picasaalbums', 'Album', '', array(
            'api' => $this->group->getConfiguration()
                                 ->getApi()
        ));
        new N2ElementOnOff($filter, 'picasarandom', n2_('Random'), 0);
    }

    function _getData($count, $startIndex) {
        $album = $this->data->get('picasaalbums', 0);
        if (!empty($album)) {
            $data = array();

            $explode  = explode("/", $album);
            $user_id  = $explode[2];
            $album_id = $explode[4];

            $isRandom = $this->data->get('picasarandom', 0);
            if (!$isRandom) {
                $album .= "?start-index=" . ($startIndex + 1) . "&max-results=" . $count;
            }

            $client = $this->group->getConfiguration()
                                  ->getApi();
            $http   = new Google_Http_Request('https://picasaweb.google.com/data/feed/api' . $album . '&alt=json', 'GET', array(
                'Content-Type' => 'application/json; charset=UTF-8',
                'Accept'       => '*/*'
            ), null);
            try {
                $request = $client->getAuth()
                                  ->authenticatedRequest($http);

                $code = $request->getResponseHttpCode();
                if ($code != 200) {
                    throw new Exception($request->getResponseBody());
                }
            } catch (Exception $e) {

                N2Message::error($e->getMessage());

                return null;
            }

            $body = $request->getResponseBody();

            $album = json_decode($body, true);

            $entries = $album['feed']['entry'];

            foreach ($entries as $photo) {
                $record              = array();
                $linkName            = $photo['content']['src'];
                $record['image']     = $linkName . "?sz=0";
                $record['thumbnail'] = $linkName;

                $record['title'] = $record['description'] = $photo['summary']['$t'];

                $record['published'] = $this->decodeDate($photo['published']['$t']);
                $record['updated']   = $this->decodeDate($photo['updated']['$t']);

                $url                 = $photo['link'][1];
                $record['url']       = $url['href'];
                $albumUrl            = explode('#', $url['href']);
                $record['album_url'] = $albumUrl[0];

                $record['google_plus_album_url'] = "https://plus.google.com/photos/" . $user_id . "/albums/" . $album_id;

                $data[] = $record;
            }

            if ($isRandom) {
                shuffle($data);

                return array_slice($data, 0, $count);
            }

            return $data;
        }

        return null;
    }

    private function decodeDate($date) {

        return str_replace(array(
            "T",
            "Z"
        ), array(
            " ",
            ""
        ), substr($date, 0, -5));
    }
}
