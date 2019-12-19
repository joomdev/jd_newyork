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
defined('_JEXEC') or die('Restricted access');

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');
require_once(JPATH_BASE.DS.'components'.DS.'com_djevents'.DS.'helpers'.DS.'djevents.php');
require_once(JPATH_BASE.DS.'components'.DS.'com_djevents'.DS.'helpers'.DS.'route.php');

$app = JFactory::getApplication();

DJEventsHelper::setAssets();
$lang = JFactory::GetLanguage();
$lang->load('com_djevents');

$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::root(true).'/modules/mod_djevents_calendar/assets/style.css');

$layout = explode(':',$params->get('layout','month'));
$layout = (isset($layout[1]) ? $layout[1] : $layout[0]);

if($layout == 'scroll') {
	$doc->addScript(JURI::root(true).'/modules/mod_djevents_calendar/assets/scroll.min.js');
} else if ($layout == 'month') {
	$doc->addScript(JURI::root(true).'/modules/mod_djevents_calendar/assets/month.min.js');
}

$items = modDJEventsCalendarHelper::getItems($params);

$params->set('module', $module->id);

require JModuleHelper::getLayoutPath('mod_djevents_calendar', $params->get('layout','month'));
