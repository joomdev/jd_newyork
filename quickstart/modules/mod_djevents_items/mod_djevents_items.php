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

$app = JFactory::getApplication();

if($app->input->get('option')=='com_djevents' && in_array($app->input->get('view','none'), $params->get('hide_on', array()))) return;

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');
require_once(JPATH_BASE.DS.'components'.DS.'com_djevents'.DS.'helpers'.DS.'djevents.php');
require_once(JPATH_BASE.DS.'components'.DS.'com_djevents'.DS.'helpers'.DS.'route.php');

DJEventsHelper::setAssets();
$lang = JFactory::GetLanguage();
$lang->load('com_djevents');

$doc = JFactory::getDocument();
//$doc->addStyleSheet(JURI::root(true).'/modules/mod_djevents_calendar/assets/style.css');

$items = modDJEventsItemsHelper::getItems($params);
$categories = modDJEventsItemsHelper::getCategories($params);

$cparams = JComponentHelper::getParams('com_djevents');
$params->def('date_format', $cparams->get('date_format'));
$params->def('time_format', $cparams->get('time_format'));

$classes = DJEventsHelper::getBSClasses();

require JModuleHelper::getLayoutPath('mod_djevents_items', $params->get('layout','default'));
