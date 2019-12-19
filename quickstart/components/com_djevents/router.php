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
defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterBase;

/**
 * Routing class of com_djevents
 *
 * @since  3.3
 */
class DJEventsRouter extends RouterBase
{
	/**
	 * Builds a URL from a query object
	 *
	 * @param array $query query object
	 *
	 * @return array
	 */
	public function build(&$query)
	{
		$segments = array();

		$app		= JFactory::getApplication();
		$menu		= $app->getMenu('site');
		
		if (empty($query['Itemid'])) {
			$menuItem = $menu->getActive();
		} else {
			$menuItem = $menu->getItem($query['Itemid']);
		}
		$mView	= (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
		$mId	= (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];
		$mCid	= (empty($menuItem->query['cid'])) ? null : $menuItem->query['cid'];
		$mCity	= (empty($menuItem->query['city'])) ? null : $menuItem->query['city'];
		
		$view = !empty($query['view']) ? $query['view'] : null;
		$id = !empty($query['id']) ? $query['id'] : null;
		$cid = !empty($query['cid']) ? $query['cid'] : null;
		$city = !empty($query['city']) ? $query['city'] : null;
		$day = !empty($query['day']) ? $query['day'] : null;
		$tag = !empty($query['tag']) ? $query['tag'] : null;
		
		$from = !empty($query['from']) ? $query['from'] : null;
		$to = !empty($query['to']) ? $query['to'] : null;
		
		// JoomSEF bug workaround
		if (isset($query['start']) && isset($query['limitstart'])) {
			if ((int)$query['limitstart'] != (int)$query['start'] && (int)$query['start'] > 0) {
				// let's make it clear - 'limitstart' has higher priority than 'start' parameter,
				// however ARTIO JoomSEF doesn't seem to respect that.
				$query['start'] = $query['limitstart'];
				unset($query['limitstart']);
			}
		}
		// JoomSEF workaround - end	
		
		unset($query['view']);
		
		if(isset($view)) {
			
			if($cid == $mCid) {
				unset($query['cid']);
			}
			if($city == $mCity) {
				unset($query['city']);
			}
			
			switch ($view) {
				case 'eventslist': {
					if ($view != $mView) {
						$segments[] = 'events';
					}
					
					if($tag && intval($tag) > 0) {
						$segments[] = 'tag';
						$segments[] = $tag;
						unset($query['tag']);
					} else if(!empty($from) && !empty($to)) {
						if($from == $to) {
							$segments[] = 'day';
							$segments[] = $from;
						} else {
							$segments[] = 'from';
							$segments[] = $from;
							$segments[] = 'to';
							$segments[] = $to;
						}
						unset($query['from']);
						unset($query['to']);
					}
					
					break;
				}
				case 'eventsweek': {
					if ($view != $mView) {
						$segments[] = 'weekly';
					}
				
					if(!empty($day)) {
						$segments[] = 'day';
						$segments[] = $day;
						unset($query['day']);
					}
				
					break;
				}
				case 'eventform': {
					if ($view != $mView) {
						$segments[] = 'edit';
					}
	
					if($id && intval($id) > 0) {
						$segments[] = $id;
						unset($query['id']);
					}
	
					break;
				}
				case 'event': {
					
					if ($view != $mView) {
						$segments[] = 'details';
					}
					
					$segments[] = $day;
					unset($query['day']);
					
					$segments[] = $id;
					unset($query['id']);
					
					break;
				}
				default: {
					
					if ($view != $mView) {
						$segments[] = $view;
					}
					
					break;
				}
			}
		}
		
		// It seems that we need to replace the ":" with "-" manually, while it's not replaced with new router system
		foreach ($segments as &$segment) {
			$segment = str_replace(':', '-', $segment);
		}
	
		return $segments;
	}
	
	/**
	 * Parse the segments into query string
	 *
	 * @param array $segments
	 *
	 * @return array
	 */
	public function parse(&$segments)
	{
		$app	= JFactory::getApplication();
		$menu	= $app->getMenu();
		$activemenu = $menu->getActive();
		$activequery = (isset($activemenu->query)) ? $activemenu->query : null;
		$db = JFactory::getDBO();
		
		$query=array();
		
		$componentViews = array('eventslist', 'events', 'eventsweek', 'weekly', 'event', 'details', 'eventform', 'edit', 'myevents');
		
		//JFactory::getApplication()->enqueueMessage("<pre>".print_r($segments, true)."</pre>");
		
		if (isset($segments[0])) {
			$viewName = $segments[0];
			if (!in_array($viewName, $componentViews)) {
				$viewName = ($activequery && isset($activequery['view'])) ? $activequery['view'] : false;
				if (!$viewName) {
					return $query;
				}
				$segments = array_merge(array($viewName), $segments);
			}
		
		
			//switch($segments[0]) {
			switch($viewName) {
				case 'events':
				case 'eventslist': {
					$query['view'] = 'eventslist';
		
					if(isset($segments[2])) {
							
						if($segments[1] == 'tag') {
		
							$query['tag'] = $segments[2];
		
						} else if($segments[1] == 'day') {
		
							$query['from'] = str_replace(':', '-', $segments[2]); // we need to get proper date format
							$query['to'] = $query['from'];
		
						} else if($segments[1] == 'from') {
		
							$query['from'] = str_replace(':', '-', $segments[2]); // we need to get proper date format
		
							if(isset($segments[4]) && $segments[3] == 'to') {
									
								$query['to'] = str_replace(':', '-', $segments[4]); // we need to get proper date format
							}
						}
					}
		
					break;
				}
				case 'weekly':
				case 'eventsweek': {
					$query['view'] = 'eventsweek';
						
					if(isset($segments[2])) {
						if($segments[1] == 'day') {
							$query['day'] = str_replace(':', '-', $segments[2]); // we need to get proper date format
						}
					}
						
					break;
				}
				case 'eventform':
				case 'edit': {
					$query['view'] = 'eventform';
					if (isset($segments[1])) {
						$query['id']= $segments[1];
					}
					break;
				}
				case 'event':
				case 'details': {
					$query['view'] = 'event';
		
					if(isset($segments[2])) {
						// not current album
						$query['day'] = str_replace(':', '-', $segments[1]); // we need to get proper date format
						$query['id'] = (int)$segments[2];
					} else {
						// shouldn't happen
					}
		
					break;
				}
				case 'myevents': {
		
					$query['view'] = $viewName;
				}
				default: {
		
					$query['view'] = 'eventslist';
		
					if(isset($segments[1])) {
							
						if($segments[0] == 'tag') {
		
							$query['tag'] = $segments[1];
		
						} else if($segments[0] == 'day') {
		
							$query['from'] = str_replace(':', '-', $segments[1]); // we need to get proper date format
							$query['to'] = $query['from'];
		
						} else if($segments[0] == 'from') {
		
							$query['from'] = str_replace(':', '-', $segments[1]); // we need to get proper date format
		
							if(isset($segments[3]) && $segments[2] == 'to') {
									
								$query['to'] = str_replace(':', '-', $segments[3]); // we need to get proper date format
							}
						}
					}
		
					break;
				}
			}
		}
		
		// [Joomla 4 Alpha 4] It seems that we have to manually reset the segments to avoid the "404 Page not found" error
		// https://issues.joomla.org/tracker/joomla-cms/21904
		if (version_compare(JVERSION, '4', 'ge'))
		{
			$segments = [];
		}
		
		if($activequery) foreach ($activequery as $key => $value) {
			if(!array_key_exists($key, $query)) $query[$key] = $value;
		}
		
		//JFactory::getApplication()->enqueueMessage("<pre>".print_r($query, true)."</pre>");
		
		return $query;
	}
}

/**
 * DJEvents router functions
 *
 * These functions are proxys for the new router interface
 * for old SEF extensions.
 *
 * @param   array  &$query  An array of URL arguments
 *
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 *
 * @deprecated  4.0  Use Class based routers instead
 */
function DJEventsBuildRoute(&$query)
{
	$router = new DJEventsRouter;
	return $router->build($query);
}

/**
 * Parse the segments of a URL.
 *
 * This function is a proxy for the new router interface
 * for old SEF extensions.
 *
 * @param   array  $segments  The segments of the URL to parse.
 *
 * @return  array  The URL attributes to be used by the application.
 *
 * @since   3.3
 * @deprecated  4.0  Use Class based routers instead
 */
function DJEventsParseRoute($segments)
{
	$router = new DJEventsRouter;
	return $router->parse($segments);
}

