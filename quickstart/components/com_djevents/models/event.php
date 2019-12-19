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

defined ('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelform');

class DJEventsModelEvent extends JModelItem {

	protected $view_event = 'event';
	protected $_event = null;
	protected $_context = 'com_djevents.item';
	
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = $app->input->get('id', 0, 'int');
		$this->setState('event.id', $pk);
		
		$start = $app->input->get('day');
		$this->setState('event.start', $start);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

	}
	
	public function getItem($pk = null, $start = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('event.id');
		$start = (!empty($start)) ? $start : $this->getState('event.start');

		if ($this->_event === null) {
			$this->_event = array();
		}

		if(!isset($this->_event[$pk])) {
			$this->_event[$pk] = array();
		}
		
		if (!isset($this->_event[$pk][$start])) {
			
			if(!$this->validateDate($start)) {
				$this->setError(JText::sprintf('COM_DJEVENTS_VALIDATE_DATE_FAILD', $start));
				$this->_event[$pk][$start] = false;
			}
			else
			try
			{
				// Create a new query object.
				$db = $this->getDbo();
				$query = $db->getQuery(true);
				
				$query->select('t.start, t.start_time, t.end, t.end_time, a.*');
				
				$query->from('#__djev_events_time AS t, #__djev_events AS a');
				
				// Join over the users for the checked out user.
				$query->select('uc.name AS editor');
				$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
				
				// Join over category
				$query->select('c.id as c_id, c.name as category_name, c.alias as category_alias, c.icon_type, c.fa_icon, c.image_icon, c.icon_color, c.icon_bg');
				$query->join('LEFT', '#__djev_cats AS c ON c.id=a.cat_id');
				
				$query->select('r.id as r_id, r.name as city_name, r.alias as city_alias');
				$query->join('LEFT', '#__djev_cities AS r ON r.id=a.city_id');
				
				$query->select('m.image, m.title as thumb_title, m.video');
				$query->join('LEFT', '#__djev_events_media AS m ON m.event_id=a.id AND m.poster=1');
				
				$query->where('t.event_id = a.id');
				$query->where('a.id ='.(int)$pk);
				$query->where('t.start >= '.$db->quote($start));
				$query->where('t.start < '.$db->quote(JFactory::getDate($start.' +1 day')));
				
				// unpublish old events
				//$query->where('t.end > '.$db->quote(JFactory::getDate('-6 months')));
				
				$db -> setQuery($query, 0, 1);
				$item = $db -> loadObject();

				if(!empty($item)) {
					$item->slug = (empty($item->alias)) ? $item->id : $item->id.':'.$item->alias;
					
					$item->category_style = ($item->icon_bg ? 'background: '.$item->icon_bg.';':'').($item->icon_color ? 'color: '.$item->icon_color.';':'');
				}
				
				$this->_event[$pk][$start] = $item;
				
			}
			catch (JException $e)
			{
				$this->setError($e);
				$this->_event[$pk][$start] = false;
			}

		}
		
		return $this->_event[$pk][$start];

	}
	
	private function validateDate($date)
	{
		$d = DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') === $date;
	}
	
	public function getMedia($pk = null) {
		
		$item = $this->getItem($pk);
		
		if(!$item) return null;
		
		$this->_db->setQuery('SELECT * FROM #__djev_events_media WHERE event_id='.$item->id.' ORDER BY poster desc, ordering asc');
		$images = $this->_db->loadObjectList();
		
		return $images;
	}
	
	public function getTags($pk = null) {
		
		$item = $this->getItem($pk);
		
		if(!$item) return null;
		
		$this->_db->setQuery('SELECT t.* FROM #__djev_tags t, #__djev_tags_xref x WHERE t.id = x.tag_id AND x.event_id='.$item->id.' ORDER BY t.name asc');
		$tags = $this->_db->loadObjectList();
	
		return $tags;
	}
	
	public function getCategory() {
		
		
	}
}