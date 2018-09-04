<?php

N2Loader::import('libraries.form.elements.list');

class N2ElementEcwidCategories extends N2ElementList {

    /** @var  N2SliderGeneratorEcwidConfiguration */
    protected $configuration;

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);


        $isConfigured = $this->configuration->checkStoreID();

        if ($isConfigured) {

            $storeID = $this->configuration->getStoreID();

            $categoriesJSON = N2TransferData::get("http://app.ecwid.com/api/v1/" . $storeID . "/categories");

            if ($categoriesJSON !== false) {
                $categoryList = json_decode($categoriesJSON);

                for ($i = 0; $i < count($categoryList); $i++) {
                    if (property_exists($categoryList[$i], "parentId")) {
                        $menuItems[$i]            = new StdClass;
                        $menuItems[$i]->parent_id = $categoryList[$i]->parentId;
                    } else {
                        $menuItems[$i]            = new StdClass;
                        $menuItems[$i]->parent_id = 0;
                    }
                    $menuItems[$i]->id    = $categoryList[$i]->id;
                    $menuItems[$i]->title = $categoryList[$i]->name;
                }

                $children = array();
                if (isset($menuItems) && $menuItems) {
                    foreach ($menuItems as $v) {
                        $pt   = $v->parent_id;
                        $list = isset($children[$pt]) ? $children[$pt] : array();
                        array_push($list, $v);
                        $children[$pt] = $list;
                    }

                    $sorted_array = $this->makeMenu($menuItems);

                    $this->options['0'] = n2_('All');
                    $this->options['-1'] = n2_('Store Front page');

                    if (is_array($sorted_array) && count($sorted_array)) {
                        foreach ($sorted_array AS $item) {
                            $this->options[$item->id] = $item->title;
                        }
                    }
                } else {
                    $this->options['0'] = 'Store ID does not configured properly or no categories in the store';
                }

                return;
            }
        }
        $this->options['0'] = 'Store ID does not configured properly';
    }


    /**
     * @param N2SliderGeneratorEcwidConfiguration $configuration
     */
    public function setConfiguration($configuration) {
        $this->configuration = $configuration;
    }

    // reordering array with makeMenu function, then build a tree; source needs array with these objects in the values: parent_id, id, title
    private function buildTree($elements, $returned = null, $parentId = 0) {
        if ($returned != null) $branch[] = $returned;
        for ($i = 0; $i < count($elements); $i++) {
            if ($elements[$i]->parent_id == $parentId) {
                $branch[] = $this->buildTree($elements, $elements[$i], $elements[$i]->id);
            }
        }

        return $branch;
    }

    private function makeMenu($menuItems) {
        $sort         = $this->buildTree($menuItems);
        $sorted_array = array();
        $array_obj    = new RecursiveIteratorIterator(new RecursiveArrayIterator($sort));
        foreach ($array_obj as $key => $value) {
            if ($key == 'id') {
                for ($i = 0; $i < count($menuItems); $i++) {
                    if ($menuItems[$i]->id == $value) {
                        array_push($sorted_array, $menuItems[$i]);
                    }
                }
            }
        }

        if (is_array($sorted_array) && count($sorted_array)) {
            foreach ($sorted_array AS $item) {
                if (!isset($pre[$item->id])) {
                    if (isset($pre[$item->parent_id])) {
                        $pre[$item->id] = $pre[$item->parent_id] . '- ';
                    } else {
                        $pre[$item->id] = '- ';
                    }
                }
                $item->title = $pre[$item->id] . htmlspecialchars($item->title);
            }
        }

        return $sorted_array;
    }

}
