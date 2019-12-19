<?php
/**
 * @package DJ-Events
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.modellist');

class DJEventsModelEvents extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'a.id', 'a.title', 'a.published', 'a.created', 'c.name', 'l.name', 't.start', 't.end'
			);
		}

		parent::__construct($config);
	}
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('t.start', 'asc');

		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);
		
		$state = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published');
		$this->setState('filter.published', $state);

		$category = $this->getUserStateFromRequest($this->context.'.filter.category', 'filter_category');
		$this->setState('filter.category', $category);

		$city = $this->getUserStateFromRequest($this->context.'.filter.city', 'filter_city');
		$this->setState('filter.city', $city);
		
		$from = $this->getUserStateFromRequest($this->context.'.filter.from', 'filter_from');
		$this->setState('filter.from', $from);
		
		$to = $this->getUserStateFromRequest($this->context.'.filter.to', 'filter_to');
		$this->setState('filter.to', $to);
		
		$tag = $app->input->getInt('tag');
		$this->setState('filter.tag', $tag);
		/* TODO:
		$featured = $this->getUserStateFromRequest($this->context.'.filter.featured', 'filter_featured');
		$this->setState('filter.featured', $featured);
		*/
		// Load the parameters.
		$params = JComponentHelper::getParams('com_djevents');
		$this->setState('params', $params);
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.serialize($this->getState('filter.category'));
		$id	.= ':'.serialize($this->getState('filter.city'));
		$id	.= ':'.$this->getState('filter.from');
		$id	.= ':'.$this->getState('filter.to');
		$id	.= ':'.$this->getState('filter.tag');
		$id	.= ':'.$this->getState('filter.featured');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select the required fields from the table.
		$select_default = 't.start, t.start_time, t.end, t.end_time, a.*, uc.name AS editor, c.id as c_id, c.name as category_name, c.alias as category_alias, 
				r.id as r_id, r.name as city_name, r.alias as city_alias, m.image, m.title as thumb_title, m.video ';

		$query->select($this->getState('list.select', $select_default));

		$query->from('#__djev_events_time AS t, #__djev_events AS a');

		// Join over the users for the checked out user.
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
		
		// Join over the users for the author user.
		//$query->join('LEFT', '#__users AS ua ON ua.id=a.created_by');
		
		// Join over groups
		$query->join('LEFT', '#__djev_cats AS c ON c.id=a.cat_id');
		
		$query->join('LEFT', '#__djev_cities AS r ON r.id=a.city_id');
		
		$query->join('LEFT', '#__djev_events_media AS m ON m.event_id=a.id AND m.poster=1');
		
		$query->where('t.event_id = a.id AND t.exclude = 0');
		
		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			else {
				$search = $db->quote('%'.$db->escape($search, true).'%');
				$searchCols = array('a.title', 'a.description', 'a.location', 'r.name', 'c.name');
				$query->where('('.implode(' LIKE '.$search.' OR ', $searchCols).' LIKE '.$search.')');
			}
		}
		
		$state = $this->getState('filter.published', false);
		if (is_numeric($state)) {
			$query->where('a.published='.(int)$state);
		} else if ($state == '') {
			$query->where('(a.published = 0 OR a.published = 1)');
		}
		
		$category = $this->getState('filter.category', false);
		if ($category) {
			if (is_array($category)) {
				ArrayHelper::toInteger($category);
				$query->where('a.cat_id IN ('.implode(',', $category).')');
			} else {
				$query->where('a.cat_id='.(int)$category);
			}
		}
		
		$city = $this->getState('filter.city', false);
		if ($city) {
			if (is_array($city)) {
				ArrayHelper::toInteger($city);
				$query->where('a.city_id IN ('.implode(',', $city).')');
			} else {
				$query->where('a.city_id='.(int)$city);
			}
		}
		
		// set the time range
		//$timezone = new DateTimeZone( JFactory::getUser()->getParam('timezone') );
		
		$from = $this->getState('filter.from');
		if(!$this->validateDate($from)) $from = 'now';
		$date = JFactory::getDate($from);
		//$date->setTimezone($timezone);
		//$app->enqueueMessage("<pre>from: ".print_r($date->toSql(), true)."</pre>");
		if($from) {
			$query->where('t.end >= '.$db->quote($date->toSql()));
		}
		
		$to = $this->getState('filter.to');
		if(!$this->validateDate($to)) $to = '+1 year';
		else $to.=' +1 day';
		$date = JFactory::getDate($to);
		//$date->setTimezone($timezone);
		//$app->enqueueMessage("<pre>to: ".print_r($date->toSql(), true)."</pre>");
		if($to) {
			$query->where('t.start < '.$db->quote($date->toSql()));
		}
		
		$tag = $this->getState('filter.tag', false);
		if($tag) {
			$query->innerJoin('#__djev_tags_xref AS x ON x.event_id=a.id AND x.tag_id='.$tag);
		}
		
		$featured = $this->getState('filter.featured', false);
		if ($featured) {
			$query->where('a.featured='.(int)$featured);
		}
		
		$own = $this->getState('filter.own', false);
		if ($own) {
			$query->where('a.created_by='.(int)$user->id);
		}
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 't.start');
		$orderDirn	= $this->state->get('list.direction', 'asc');
		
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}
	
	protected function validateDate($date)
	{
		$d = DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') === $date;
	}
	
	public function getCategories() {
		$this->_db->setQuery('SELECT id as value, name as text FROM #__djev_cats ORDER BY name ASC');
		$cats = $this->_db->loadObjectList();
		return $cats;
	}
	
	public function getCities() {
		$this->_db->setQuery('SELECT id as value, name as text FROM #__djev_cities ORDER BY name ASC');
		$cities = $this->_db->loadObjectList();
		return $cities;
	}
	
}