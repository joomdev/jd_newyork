<?php

N2Loader::import('libraries.form.element.list');

class N2ElementZooCategories extends N2ElementList {

    private $categories = array();

    protected $appid;

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_zoo' . DIRECTORY_SEPARATOR . 'config.php');

        $app = App::getInstance('zoo')->table->application->get($this->appid);

        $categories = $app->getCategories(true, null, true);

        $this->options['0'] = n2_('All');

        if (count($categories)) {
            foreach ($categories AS $category) {
                if (!isset($this->categories[$category->parent])) $this->categories[$category->parent] = array();
                $this->categories[$category->parent][] = $category;
            }
            $this->renderCategory(0, ' - ');
        }
    }

    function renderCategory($parent, $pre) {
        if (isset($this->categories[$parent])) {
            foreach ($this->categories[$parent] AS $category) {
                $this->options[$category->id] = $pre . $category->name;

                $this->renderCategory($category->id, $pre . ' - ');
            }
        }
    }

    /**
     * @param mixed $appid
     */
    public function setAppid($appid) {
        $this->appid = $appid;
    }

}
