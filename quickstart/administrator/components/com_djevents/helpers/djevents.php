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

class DJEventsHelper
{
	public static function addSubmenu($vName = 'cpanel')
	{
		$app = JFactory::getApplication();
		
		JHtmlSidebar::addEntry(JText::_('COM_DJEVENTS_CPANEL'), 'index.php?option=com_djevents&view=cpanel', $vName=='cpanel');
		JHtmlSidebar::addEntry(JText::_('COM_DJEVENTS_CATEGORIES'), 'index.php?option=com_djevents&view=categories', $vName=='categories');
		JHtmlSidebar::addEntry(JText::_('COM_DJEVENTS_EVENTS'), 'index.php?option=com_djevents&view=events', $vName=='events');
	}

	public static function getActions($asset = null, $assetId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if ( !$asset) {
			$assetName = 'com_djevents';
		} else if ($assetId != 0){
			$assetName = 'com_djevents.'.$asset.$assetId;
		} else {
			$assetName = 'com_djevents.'.$asset;
		}

		$actions = array(
			'core.admin', 'core.manage'
		);
		
		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
	
	public static function getBSClasses() {
		
		$classes = new JObject;
		
		if(version_compare(JVERSION, '4', '>=')) { // Bootstrap 4
			$classes->set('row', 'row');
			$classes->set('col', 'col-md-');
		} else { // Boostrap 2.3.2
			$classes->set('row', 'row-fluid');
			$classes->set('col', 'span');
		}
		
		return $classes;
	}
}
