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

class modDJEventsCalendarHelper
{
	static $navi = null;
	
    static function getItems(&$params) {
    	
    	$app = JFactory::getApplication();
    	$option = $app->input->get('option');
    	$view   = $app->input->get('view');
    	
    	$items = array();
    	
    	$cparams = JComponentHelper::getParams('com_djevents');
    	
    	JModelLegacy::addIncludePath(JPATH_BASE.'/components/com_djevents/models', 'DJEventsModel');
    	$model = JModelLegacy::getInstance('EventsList', 'DJEventsModel', array('ignore_request'=>true));
    	
		$layout = explode(':',$params->get('layout','month'));
		$layout = (isset($layout[1]) ? $layout[1] : $layout[0]);
		
		$start_date = $params->get('start_date','now');
		
		$date = JFactory::getDate($start_date);
		
		if($layout == 'scroll') {
			
			$days = (int) $params->get('days', 3);
			if(!$days) $days = 3;
			
			$start = $date->format('Y-m-d');
			$end = JFactory::getDate($start.' +'.$days.' days')->format('Y-m-d');
			
		} else {
			
			$start = $date->format('Y-m-'.'01');
			$end = JFactory::getDate($start.' +1 month')->format('Y-m-d');
		}
		
		$select = 't.start, t.start_time, t.end, t.end_time, a.id, a.title, a.alias, a.cat_id, a.city_id';
		
		//$state = $model->getState();
		
		$model->setState('list.select', $select);
		
		$model->setState('filter.from', $start);
		$model->setState('filter.to', $end);
		
		$model->setState('list.start', 0);
		$model->setState('list.limit', 0);
		
		$model->setState('list.ordering', 't.start');
		$model->setState('list.direction', 'asc');
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
		
		/* filter by featured */
		if((int)$params->get('featured_only',0) == 1) {
			$model->setState('filter.featured', 1);
		}
		
		/* get events */
		$events = $model->getItems();
		
		$from = JFactory::getDate($start)->toUnix();
		$to = JFactory::getDate($end)->toUnix();
		
		if(count($events)) {
			foreach($events as $event) {
				
				$tmp = explode(' ', $event->start);
				$start = JFactory::getDate(@$tmp[0]);
				$start_day = $start->format('Y-m-d');
				$time = JFactory::getDate($event->start)->format($cparams->get('time_format', 'h:i a'));
				if($start->toUnix() < $from) $start = JFactory::getDate($from);
				
				$tmp = explode(' ', $event->end);
				$end = JFactory::getDate(@$tmp[0])->toUnix();
				if($end > $to) $end = $to;
				
				$event->link = JRoute::_(DJEventsHelperRoute::getEventRoute($event->id.':'.$event->alias, $event->start, $event->cat_id, $event->city_id));
				
				while($start->toUnix() <= $end) {
					$day = $start->format('Y-m-d');
					if(!isset($items[$day])) $items[$day] = array();
					$event->time = $start_day == $day ? $time : 0;
					$items[$day][] = clone($event);
					$start = JFactory::getDate($day.' +1 day');
				}
			}
			unset($event);
		}
		
		return $items;
    }
	
}
