<?php
N2Loader::import('libraries.form.element.list');

class N2ElementJReviewsArticles extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $db = JFactory::getDBO();

        $query = 'SELECT asset_id, title FROM #__content WHERE catid IN (SELECT id FROM #__jreviews_categories WHERE `option` = \'com_content\' AND criteriaid IN
                      (SELECT id FROM #__jreviews_criteria WHERE state <> 0))';

        $db->setQuery($query);
        $articles = $db->loadObjectList();

        $this->options['0'] = n2_('All');

        if (count($articles)) {
            foreach ($articles AS $article) {
                $this->options[$article->asset_id] = $article->title;
            }
        }

    }

}
