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
 
// no direct access
defined('_JEXEC') or die ('Restricted access');

require_once(JPath::clean(JPATH_ROOT.'/administrator/components/com_djevents/lib/image.php'));

class modDJEventsItemsHelper
{
	
    static function getItems(&$params) {
    	
    	$app = JFactory::getApplication();
    	$option = $app->input->get('option');
    	$view   = $app->input->get('view');
    	$hideOn = $params->get('hide_on', array());
    	
    	JModelLegacy::addIncludePath(JPATH_BASE.'/components/com_djevents/models', 'DJEventsModel');
    	$model = JModelLegacy::getInstance('EventsList', 'DJEventsModel', array('ignore_request'=>true));
    	
		$model->setState('list.start', 0);
		$model->setState('list.limit', $params->get('limit', 5));
		$model->setState('list.ordering', 't.start');
		$model->setState('filter.published', 1);
		
		/* categories and cities filtering */
		$categories = $params->get('categories',array());
		$cities = $params->get('cities',array());
		
		if(($params->get('follow_category',0) || $params->get('follow_city',0)) && $option == 'com_djevents') {
			
			$cat_id = null;
			$city_id = null;
			
			switch ($view)
			{
				case 'eventslist' :
				case 'eventsweek' :
					$cat_id = $app->input->getInt('cid');
					$city_id = $app->input->getInt('city');
					break;
				case 'event' :
					$event_id 	= $app->input->getInt('id');
					$day 		= $app->input->get('day');
					// Get an instance of the generic article model
					$event = JModelLegacy::getInstance('Event', 'DJEventsModel', array('ignore_request' => true));
					
					$event->setState('filter.published', 1);
					$event->setState('event.id', (int) $event_id);
					$event->setState('event.start', $day);
					$event->setState('params', $app->getParams());
					
					$item = $event->getItem();
					
					if($item) {
						$cat_id = $item->cat_id;
						$city_id = $item->city_id;
					}
					
				break;
			}
			
			if($params->get('follow_category',0) && $cat_id) {
				if(empty($categories) || in_array($cat_id, $categories)) {
					$categories = array($cat_id);
				}
			}
			
			if($params->get('follow_city',0) && $city_id) {
				if(empty($cities) || in_array($city_id, $cities)) {
					$cities = array($city_id);
				}
			}
		}
		
		$model->setState('filter.category', $categories);
		$model->setState('filter.city', $cities);
		
		/* time range filtering */
		$range = (int)$params->get('range',0);		
		if($params->get('type','upcoming') == 'upcoming') { // upcoming events
			
			$model->setState('list.direction', 'asc');
			
			$from = JFactory::getDate()->format('Y-m-d');
			$model->setState('filter.from', $from);
			if($range > 0) {
				$to = JFactory::getDate('+'.$range.' days')->format('Y-m-d');
				$model->setState('filter.to', $to);
			}
			
		} else { // past events
			
			$model->setState('list.direction', 'desc');
			
			$to = JFactory::getDate()->format('Y-m-d');
			$model->setState('filter.to', $to);
			if($range > 0) {
				$from = JFactory::getDate('-'.$range.' days')->format('Y-m-d');
				$model->setState('filter.from', $from);
			} else {
				$from = JFactory::getDate('-1 year')->format('Y-m-d');
				$model->setState('filter.from', $from);
			}
		}
		
		/* filter by featured */
		if((int)$params->get('featured_only',0) == 1) {
			$model->setState('filter.featured', 1);
		}
		
		/* get events */
		$items = $model->getItems();
		
		if(count($items)) {
			foreach($items as $item) {
				
				$item->link = JRoute::_(DJEventsHelperRoute::getEventRoute($item->id.':'.$item->alias, $item->start, $item->cat_id, $item->city_id));
				
				if(empty($item->image)) continue;
				
				if(!$item->thumb = DJEventsImageResizer::createThumbnail($item->image, 'media/djevents/images', $params->get('thumb_width', 200),
						$params->get('thumb_height', 200), $params->get('thumb_resizing', 'crop'), $params->get('thumb_quality', 75))) {
					$item->thumb = $item->image;
				}
				if(strcasecmp(substr($item->thumb, 0, 4), 'http') != 0 && !empty($item->thumb)) {
					$item->thumb = JURI::root(true).'/'.$item->thumb;
				}
			}
		}
		
		return $items;
    }
	
    static function getCategories(&$params) {
    	
    	JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_djevents/models', 'DJEventsModel');
    	$model = JModelLegacy::getInstance('Categories', 'DJEventsModel', array('ignore_request'=>true));
    	
    	$model->setState('list.start',0);
    	$model->setState('list.limit',0);
    	
    	return $model->getItems();
    }
}
