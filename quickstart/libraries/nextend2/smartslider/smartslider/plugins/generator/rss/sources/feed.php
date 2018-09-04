<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorRSSFeed extends N2GeneratorAbstract {

    protected $layout = 'article';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        new N2ElementText($filter, 'rssurl', 'RSS url', '', array(
            'style' => 'width:600px;'
        ));

        $group = new N2ElementGroup($filter, 'configuration', n2_('Configuration'));
        new N2ElementText($group, 'dateformat', n2_('Date format'), 'm-d-Y');
        new N2ElementTextarea($filter, 'sourcetranslatedate', n2_('Translate date and time'), 'January->January||February->February||March->March', array(
            'fieldStyle' => 'width:300px;height: 100px;'
        ));
    }

    protected function _getData($count, $startIndex) {
        $url             = $this->data->get('rssurl', '');
        $date_format     = $this->data->get('dateformat', 'Y-m-d');
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

        $content = N2TransferData::get($url);
        if ($content === false) {
            return null;
        }
        try {
            $xml = new SimpleXmlElement($content);
        } catch (Exception $e) {
            N2Message::error(n2_('The data in the given url is not valid XML.'));

            return null;
        }
        $data = array();
        $i    = 0;

        $atom = false;
        if (isset($xml->channel->item)) {
            $entries = $xml->channel->item;
        } else if (isset($xml->entry)) {
            $entries = $xml->entry;
            $atom    = true;
        }

        foreach ($entries as $entry) {
            foreach ($entry AS $key => $value) {
                $val = (string)$value;
                foreach ($value AS $inner_key => $inner_val) {
                    $data[$i][$key . '_' . $inner_key] = $inner_val;
                }
                if (!empty($val)) {
                    if ($this->checkIsAValidDate($val)) {
                        $val = $this->translate(date($date_format, strtotime($val)), $translate);
                    }
                    $data[$i][$key] = $val;
                }
                $attributes = $entry->$key->attributes();
                if (!empty($attributes)) {
                    foreach ($attributes AS $attribute => $attribute_val) {
                        $attribute_val_str = @(string)$attribute_val;
                        if (isset($attribute_val_str)) {
                            $data[$i][$key . '_' . $attribute] = $attribute_val_str;
                        }
                    }
                }
            }
            $group = $entry->children('http://search.yahoo.com/mrss/')->group;
            foreach ($group AS $group_name => $group_data) {
                foreach ($group_data AS $group_key => $group_val) {
                    $group_val_str = @(string)$attribute_val;
                    if (isset($group_val_str)) {
                        $data[$i][$group_name . '_' . $group_key] = $group_val_str;
                    }
                    $attributes = $group_data->$group_key->attributes();
                    if (!empty($attributes)) {
                        foreach ($attributes AS $attribute => $attribute_val) {
                            $attribute_val_str = @(string)$attribute_val;
                            if (isset($attribute_val_str)) {
                                $data[$i][$group_name . '_' . $group_key . '_' . $attribute] = $attribute_val_str;
                            }
                        }
                    }
                }
            }
            if ($atom) {
                $content = @(string)$entry->content;
            } else {
                $content = @(string)$entry->children('http://purl.org/rss/1.0/modules/content/')->encoded;
            }
            if (!empty($content)) {
                $data[$i]['content'] = $content;
            }
            $i++;
            if ($i == $count + $startIndex) break;
        }
        $data = array_slice($data, $startIndex, $count);
        return $data;
    }

    protected function checkIsAValidDate($dateString) {
        return (bool)strtotime($dateString);
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
