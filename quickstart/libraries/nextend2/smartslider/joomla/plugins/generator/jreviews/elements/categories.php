<?php
N2Loader::import('libraries.form.element.list');

class N2ElementJReviewsCategories extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $db = JFactory::getDBO();

        $query = 'SELECT asset_id FROM #__categories WHERE id IN
                    (SELECT id FROM #__jreviews_categories WHERE `option` = \'com_content\' AND criteriaid IN
                      (SELECT id FROM #__jreviews_criteria WHERE state <> 0))';

        $db->setQuery($query);
        $ratableItems = $db->loadColumn();

        $query = 'SELECT title, asset_id, id, parent_id FROM #__categories';
        $db->setQuery($query);
        $allItems = $db->loadObjectList();
        $children = array();
        if ($allItems) {
            foreach ($allItems as $v) {
                $pt   = $v->parent_id;
                $list = isset($children[$pt]) ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }

        $this->options['0'] = n2_('All');

        jimport('joomla.html.html.menu');
        $options = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
        if (count($options)) {
            foreach ($options AS $option) {
                if (in_array($option->asset_id, $ratableItems)) {
                    $this->options[$option->asset_id] = $option->treename;
                }
            }
        }
    }

}
