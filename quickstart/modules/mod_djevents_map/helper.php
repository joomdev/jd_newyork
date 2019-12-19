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

require_once(JPath::clean(JPATH_ROOT.'/modules/mod_djevents_items/helper.php'));

class modDJEventsMapHelper extends modDJEventsItemsHelper
{
	
    static function getPoints(&$params) {
    	
    	$points = array();
    	$items = self::getItems($params);
    	
    	foreach($items as $item) {
    		
    		if ($item->latitude == '' || $item->latitude == 0.00000000 || $item->longitude == '' || $item->longitude == 0.00000000) {
    			continue;
    		}
    		
    		$key = $item->latitude.'x'.$item->longitude;

    		if(!isset($points[$key])) $points[$key] = array();
    		
    		$points[$key][] = $item;
    	}
    	
    	return $points;
    }
}
