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

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

require_once(JPATH_BASE.DS.'components'.DS.'com_djevents'.DS.'helpers'.DS.'djevents.php');

$app = JFactory::getApplication();

DJEventsHelper::setAssets();
$lang = JFactory::GetLanguage();
$lang->load('com_djevents');

JModelLegacy::addIncludePath('components/com_djevents/models', 'DJEventsModel');
$model = JModelLegacy::getInstance('EventsList', 'DJEventsModel', array('ignore_request'=>true));

$options = array();
$options['categories'] = $model->getCategories();
$options['cities'] = $model->getCities();

$Itemid = $app->input->get('Itemid', 0, 'int');

require(JModuleHelper::getLayoutPath('mod_djevents_search'));
