<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorFacebookPostsByPage extends N2GeneratorAbstract {

    protected $layout = 'image';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        new N2ElementText($filter, 'page', n2_('Page'), 'Nextendweb');
        new N2ElementRadio($filter, 'endpoint', n2_('Type'), 'posts', array(
            'options' => array(
                'posts' => n2_('Posts'),
                'feed'  => n2_('Feed')
            )
        ));

        $group = new N2ElementGroup($filter, 'configuration', n2_('Configuration'));
        new N2ElementText($group, 'dateformat', n2_('Date format'), 'm-d-Y');
        new N2ElementText($group, 'timeformat', n2_('Time format'), 'H:i:s');
        new N2ElementOnOff($group, 'remove_spec_chars', n2_('Remove special characters'), 0);
        new N2ElementTextarea($filter, 'sourcetranslatedate', n2_('Translate date and time'), 'January->January||February->February||March->March', array(
            'fieldStyle' => 'width:300px;height: 100px;'
        ));
    }

    protected function _getData($count, $startIndex) {

        $api = $this->group->getConfiguration()
                           ->getApi();

        $data = array();
        try {
            $result = $api->sendRequest('GET', $this->data->get('page', 'nextendweb') . '/' . $this->data->get('endpoint', 'feed'), array(
                'offset' => $startIndex,
                'limit'  => $count,
                'fields' => implode(',', array(
                    'from',
                    'updated_time',
                    'link',
                    'picture',
                    'source',
                    'description',
                    'message',
                    'story',
                    'type',
                    'full_picture'
                ))
            ))
                          ->getDecodedBody();

            for ($i = 0; $i < count($result['data']); $i++) {
                $post              = $result['data'][$i];
                $record['link']    = isset($post['link']) ? $post['link'] : '';
                $remove_spec_chars = $this->data->get("remove_spec_chars", 0);
                if ($remove_spec_chars) {
                    if (isset($post['message']) && !empty($post['message'])) {
                        $description           = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $this->makeClickableLinks($post['message']));
                        $record['description'] = str_replace("\n", "<br/>", $description);
                    } else {
                        $record['description'] = "";
                    }
                } else {
                    $record['description'] = isset($post['message']) ? str_replace("\n", "<br/>", $this->makeClickableLinks($post['message'])) : '';
                }
                $record['message'] = $record['description'];
                $record['story']   = isset($post['story']) ? $this->makeClickableLinks($post['story']) : '';
                $record['type']    = $post['type'];
                $record['image']   = isset($post['full_picture']) ? $post['full_picture'] : '';

                $sourceTranslate = $this->data->get('sourcetranslatedate', '');
                $translateValue  = explode('||', $sourceTranslate);
                $translate       = array();
                if ($sourceTranslate != 'January->January||February->February||March->March' && !empty($translateValue)) {
                    foreach ($translateValue AS $tv) {
                        $translateArray = explode('->', $tv);
                        if (!empty($translateArray) && count($translateArray) == 2) {
                            $translate[$translateArray[0]] = $translateArray[1];
                        }
                    }
                }
                $record['date'] = $this->translate(date($this->data->get('dateformat', 'Y-m-d'), strtotime($result['data'][$i]['updated_time'])), $translate);
                $record['time'] = date($this->data->get('timeformat', 'H:i:s'), strtotime($result['data'][$i]['updated_time']));


                $data[$i] = &$record;
                unset($record);
            }
        } catch (Exception $e) {
            N2Message::error($e->getMessage());
        }

        return $data;
    }

    private function translate($from, $translate) {
        if (!empty($translate) && !empty($from)) {
            foreach ($translate AS $key => $value) {
                $from = str_replace($key, $value, $from);
            }
        }

        return $from;
    }
}