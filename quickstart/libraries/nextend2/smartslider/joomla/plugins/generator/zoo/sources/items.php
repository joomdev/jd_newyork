<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');


class N2GeneratorZooItems extends N2GeneratorAbstract {

    protected $layout = 'article';

    protected $appid, $identifier;

    public function __construct($group, $name, $label, $appid, $identifier) {
        parent::__construct($group, $name, $label);
        $this->appid      = $appid;
        $this->identifier = $identifier;
    }

    public function renderFields($form) {
        parent::renderFields($form);

        $filter = new N2Tab($form, 'filter', n2_('Filter'));

        $source = new N2ElementGroup($filter, 'source', n2_('Source'));
        new N2ElementZooCategories($source, 'zooitemssourcecategories', n2_('Category'), 0, array(
            'isMultiple' => true,
            'appid'      => $this->appid
        ));
        new N2ElementZooTags($source, 'zooitemssourcetags', n2_('Tag'), 0, array(
            'isMultiple' => true
        ));

        $limit = new N2ElementGroup($filter, 'limit', n2_('Limit'));
        new N2ElementOnoff($limit, 'zoofrontpage', n2_('Frontpage'), 0);

        $_order = new N2Tab($form, 'order', n2_('Order by'));
        $order  = new N2ElementMixed($_order, 'zooitemsorder', n2_('Order'), 'a.created|*|desc');
        new N2ElementList($order, 'zooitemsorder-1', n2_('Field'), '', array(
            'options' => array(
                ''           => n2_('None'),
                'a.name'     => n2_('Name'),
                'a.hits'     => n2_('Hits'),
                'a.created'  => n2_('Creation time'),
                'a.modified' => n2_('Modification time'),
                'a.priority' => n2_('Priority')
            )
        ));

        new N2ElementRadio($order, 'zooitemsorder-2', n2_('order'), '', array(
            'options' => array(
                'asc'  => n2_('Ascending'),
                'desc' => n2_('Descending')
            )
        ));

    }

    protected function _getData($count, $startIndex) {

        $data      = array();
        $appId     = $this->appid;
        $typeAlias = $this->identifier;

        require_once(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_zoo' . DIRECTORY_SEPARATOR . 'config.php');
        $this->zoo = App::getInstance('zoo');

        $app   = $this->zoo->table->application->get($appId);
        $table = $this->zoo->table->item;

        $select = 'a.*';
        $from   = $table->name . ' AS a';

        $where = array();

        $where[] = 'a.application_id = ' . $appId;
        $where[] = "a.type = '" . $typeAlias . "'";
        $where[] = "a.state = 1";

        $now     = $this->zoo->date->create()
            ->toSQL();
        $null    = $this->zoo->database->getNullDate();
        $where[] = "(a.publish_up = '" . $null . "' OR a.publish_up < '" . $now . "')";
        $where[] = "(a.publish_down = '" . $null . "' OR a.publish_down > '" . $now . "')";

        $where[] = 'a.' . $this->zoo->user->getDBAccessString($this->zoo->user->get());

        $categories = array_map('intval', explode('||', $this->data->get('zooitemssourcecategories', '0')));
        $frontpage  = $this->data->get('zoofrontpage', '0');
        if (($categories && !in_array(0, $categories)) || $frontpage) {
            if ($frontpage) {
                $categories = array( 0 );
            }
            $from    .= ' LEFT JOIN ' . ZOO_TABLE_CATEGORY_ITEM . ' AS ci ON a.id = ci.item_id';
            $where[] = 'ci.category_id IN (' . implode(',', $categories) . ') ';
        }

        $tags = explode('||', $this->data->get('zooitemssourcetags', 'All'));
        if (!empty($tags[0]) && !in_array('0', $tags)) {
            $where[] = 'a.id IN (SELECT item_id FROM #__zoo_tag WHERE name IN (' . implode(',', $tags) . ')) ';
        }

        $options = array(
            'select'     => $select,
            'from'       => $from,
            'conditions' => array( implode(' AND ', $where) ),
            'group'      => 'a.id',
            'offset'     => $startIndex,
            'limit'      => $count + $startIndex
        );

        $order = N2Parse::parse($this->data->get('zooitemsorder', 'a.name|*|asc'));
        if ($order[0]) {
            $options += array(
                'order' => $order[0] . ' ' . $order[1] . ' '
            );
        }

        $items = $table->all($options);
        $i     = 0;

        $types = $app->getTypes();
        $skip  = array(
            'supercontact',
            'linkpro',
            'downloadpro'
        );
        foreach ($items AS $item) {
            $typeElements      = $types[$typeAlias]->getElements();
            $data[$i]          = array();
            $data[$i]['title'] = $item->name;
            $data[$i]['url']   = $this->zoo->route->item($item);
            $data[$i]['hits']  = $item->hits;

            $fields = array();
            foreach ($typeElements AS $k => $el) {
                $type = $el->config->get('type');
                if (in_array($type, $skip)) continue;
                $el->setItem($item);
                $name     = str_replace('-', '', $type . '_' . $k);
                $fields[] = $data[$i][$name] = @$el->render();
            }
            $data[$i]['image'] = $data[$i]['thumbnail'] = N2JoomlaImageFallBack::fallback('', array(), $fields);

            $i++;
        }

        return $data;
    }

}
