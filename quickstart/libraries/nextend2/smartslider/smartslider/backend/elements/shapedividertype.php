<?php
N2Loader::import('libraries.form.elements.list');

class N2ElementShapeDividerType extends N2ElementList {

    protected function renderOptions($options) {

        $html = '<option value="0" ' . $this->isSelected('0') . '>' . n2_('Disabled') . '</option>';

        $folder = NEXTEND_SMARTSLIDER_ASSETS . '/shapedivider/';
        $html .= $this->folderToOptions($folder, 'simple-');

        $html .= N2HTML::tag('optgroup', array('label' => '2 colors'), $this->folderToOptions($folder . 'bicolor/', 'bi-', ''));

        return $html;
    }

    private function folderToOptions($folder, $preValue, $preLabel = '') {
        $html  = '';
        $files = N2Filesystem::files($folder);

        sort($files);
        $extension = 'svg';

        for ($i = 0; $i < count($files); $i++) {
            $pathInfo = pathinfo($files[$i]);
            if (isset($pathInfo['extension']) && $pathInfo['extension'] == $extension) {
                $html .= '<option value="' . $preValue . $pathInfo['filename'] . '" ' . $this->isSelected($pathInfo['filename']) . '>' . $preLabel . ucfirst($pathInfo['filename']) . '</option>';
            }
        }

        return $html;
    }
}
