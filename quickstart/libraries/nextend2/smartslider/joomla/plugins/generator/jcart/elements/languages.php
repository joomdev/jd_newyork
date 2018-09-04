<?php
N2Loader::import('libraries.form.element.list');
require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jcart' . DIRECTORY_SEPARATOR . 'config.php');

class N2ElementJCartLanguages extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);
        $db = JFactory::getDBO();

        $query = 'SELECT language_id, name FROM ' . DB_PREFIX . 'language';

        $db->setQuery($query);
        $options = $db->loadObjectList();

        $this->options['0'] = n2_('All');

        if (count($options)) {
            foreach ($options AS $option) {
                $this->options[$option->language_id] = $option->name;
            }
        }
    }

}
