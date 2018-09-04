<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorTextText extends N2GeneratorAbstract {

    protected $layout = 'image';

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'album', n2_('Album'));

        new N2ElementText($filter, 'sourcefile', 'CSV url', '', array(
            'style' => 'width:600px;'
        ));

        new N2ElementText($filter, 'delimiter', 'Column delimiter', ',', array(
            'style' => 'width:100px;'
        ));
    }

    protected function _getData($count, $startIndex) {
        $delimiter = $this->data->get('delimiter', ',');
        $source    = $this->data->get('sourcefile', '');

        $content = N2TransferData::get($source);
        $lines   = preg_split('/$\R?^/m', $content);
        $data    = array();
        if (!empty($lines)) {
            $i = 0;
            $k = 0;
            for ($i = 0; $i < count($lines) && ($count + $startIndex) > $i; $i++) {
                if ($startIndex <= $i) {
                    $parts = explode($delimiter, $lines[$i]);
                    $j     = 1;
                    foreach ($parts AS $part) {
                        $data[$k]['variable' . $j] = $part;
                        $j++;
                    }
                    $k++;
                }
            }
        }

        return $data;
    }
}