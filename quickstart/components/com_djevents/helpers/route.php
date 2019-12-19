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

jimport('joomla.application.component.helper');

class DJEventsHelperRoute
{
	protected static $lookup;
	
	public static function getEventRoute($id, $start, $cid = null, $city = null) {
		
		$needles = array(
				'event' => array((int)$id),
				'eventslist'  => array(0)
		);
		
		$start = JFactory::getDate($start)->format('Y-m-d');
		
		//Create the link
		$link = 'index.php?option=com_djevents&view=event&day='.$start.'&id='.$id;
		
		if(!empty($cid)) {
			$tmp_needles = array();
			foreach($needles['eventslist'] as $val) {
				$tmp_needles[] = (int)$cid;
			}
			$needles['eventslist'] = array_merge($tmp_needles, $needles['eventslist']);
		}
		
		if(!empty($city)) {
			$tmp_needles = array();
			foreach($needles['eventslist'] as $val) {
				$tmp_needles[] = $val.'c'.(int)$city;
			}
			$needles['eventslist'] = array_merge($tmp_needles, $needles['eventslist']);
		}
		
		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
		
		return $link;
	}
	
	public static function getEventFormRoute($id = 0)
	{
		$needles = array(
				'eventform'  => array(0)
		);
	
		//Create the link
		$link = 'index.php?option=com_djevents&view=eventform'.($id ? '&id='.$id:'');
	
		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
	
		return $link;
	}
	
	public static function getEventsListRoute($cid = null, $city = null)
	{
		$needles = array(
			'eventslist'  => array(0)
		);
		
		//Create the link
		$link = 'index.php?option=com_djevents&view=eventslist';
		
		if(!empty($cid)) {
			$link .= '&cid='.$cid;
			$tmp_needles = array();
			foreach($needles['eventslist'] as $val) {
				$tmp_needles[] = (int)$cid;
			}
			$needles['eventslist'] = array_merge($tmp_needles, $needles['eventslist']);
		}
		
		if(!empty($city)) {
			$link .= '&city='.$city;
			$tmp_needles = array();
			foreach($needles['eventslist'] as $val) {
				$tmp_needles[] = $val.'c'.(int)$city;
			}
			$needles['eventslist'] = array_merge($tmp_needles, $needles['eventslist']);
		}
		
		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
		
		return $link;
	}
	
	public static function getEventsWeekRoute($day = null, $cid = null, $city = null)
	{
		$needles = array(
				'eventsweek'  => array(0)
		);
	
		//Create the link
		$link = 'index.php?option=com_djevents&view=eventsweek';
		
		if ($day) {
			$link .= '&day='.$day;
		}
		
		if(!empty($cid)) {
			$link .= '&cid='.$cid;
			$tmp_needles = array();
			foreach($needles['eventsweek'] as $val) {
				$tmp_needles[] = (int)$cid;
			}
			$needles['eventsweek'] = array_merge($tmp_needles, $needles['eventsweek']);
		}
		
		if(!empty($city)) {
			$link .= '&city='.$city;
			$tmp_needles = array();
			foreach($needles['eventsweek'] as $val) {
				$tmp_needles[] = $val.'c'.(int)$city;
			}
			$needles['eventsweek'] = array_merge($tmp_needles, $needles['eventsweek']);
		}
		
		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
	
		return $link;
	}
	
	public static function getMyEventsRoute()
	{
		$needles = array(
				'myevents'  => array(0),
				'eventslist'  => array(0)
		);
	
		//Create the link
		$link = 'index.php?option=com_djevents&view=myevents';
	
		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}
	
		return $link;
	}
	
	public static function _findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');

		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();

			$component	= JComponentHelper::getComponent('com_djevents');
			$items		= $menus->getItems('component_id', $component->id);
			if (count($items)) {
				foreach ($items as $item)
                {
                    if (isset($item->query) && isset($item->query['view']))
                    {
                        $view = $item->query['view'];
                        if (!isset(self::$lookup[$view])) {
                            self::$lookup[$view] = array();
                        }
                        
                        $id = isset($item->query['cid']) ? $item->query['cid'] : 0;
                        
                        if (in_array($view, array('eventslist','eventsweek')) && isset($item->query['city']) && (int)$item->query['city'] > 0) {
                        	//self::$lookup[$view][$id] = $item->id;
                        	$id .= 'c'.$item->query['city'];
                        }
                        
                        self::$lookup[$view][$id] = $item->id;
                    }
                }
            }
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view]))
				{
					if (is_array($ids)) {
						foreach($ids as $id)
						{
							if (isset(self::$lookup[$view][$id])) {
								return self::$lookup[$view][$id];
							}
						}
					} else if (isset(self::$lookup[$view][$ids])) {
						return self::$lookup[$view][$ids];
					}
				}
			}
		} else {
			
			$active = $menus->getActive();
			if ($active && $active->component == 'com_djevents') {
				return $active->id;
			}
		}

		return null;
	}
	
}
?>
