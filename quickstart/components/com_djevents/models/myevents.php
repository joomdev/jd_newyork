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

class DJEventsModelMyEvents extends DJEventsModelEvents
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
		
		$this->setState('params', $params);
		$this->setState('filter.own', true);
	}
}