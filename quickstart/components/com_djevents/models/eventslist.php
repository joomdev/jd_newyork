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

require_once JPath::clean(JPATH_ADMINISTRATOR.'/components/com_djevents/models/events.php');

jimport('joomla.application.component.helper');
jimport('joomla.html.pagination');

class DJEventsModelEventsList extends DJEventsModelEvents
{
	public function __construct($config = array()) {
		parent::__construct($config);
	}
	
	public function populateState($ordering = null, $direction = null) {
		
		// This is ignored when calling a model from API scope

		// List state information.
		parent::populateState('t.start', 'asc');
		
		$app = JFactory::getApplication();
		$params = $app->getParams('com_djevents');
		
		$search = $app->input->getString('search');
		$this->setState('filter.search', $search);
		
		$category = $app->input->getInt('cid');
		$this->setState('filter.category', $category);
		
		$city = $app->input->getInt('city');
		$this->setState('filter.city', $city);
		
		$from = $app->input->getString('from');
		$this->setState('filter.from', $from);
		
		$to = $app->input->getString('to');
		$this->setState('filter.to', $to);
		
		$start = $app->input->getInt('limitstart', 0);
		$limit = $app->input->getInt('limit', $params->get('evlist_limit', 10));
		
		$this->setState('list.start', $start);
		$this->setState('list.limit', $limit);
		
		$this->setState('filter.published', 1);
	}
	
	function getTag() {
		
		$app = JFactory::getApplication();
		
		$tag = $app->input->get('tag');
		
		if(!empty($tag) && intval($tag) > 0) {
			
			$this->_db->setQuery('SELECT * FROM #__djev_tags WHERE id='.(int)$tag);
			$tag = $this->_db->loadObject();
			
			return $tag;
		}
		
		return false;
	}
	
	function getStoreKey(){
		return $this->getStoreId();
	}
}